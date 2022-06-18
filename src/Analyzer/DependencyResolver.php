<?php
declare(strict_types=1);

namespace Tasuku43\DependencyAnalyzer\Analyzer;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\Error;
use PhpParser\ParserFactory;

class DependencyResolver
{
    public function __construct(
        private Parser        $parser,
        private NodeTraverser $traverser,
        private NodeFinder    $finder
    )
    {
    }

    public static function factory(): self
    {
        return new self(
            parser: (new ParserFactory)->create(ParserFactory::PREFER_PHP7),
            traverser: new NodeTraverser(),
            finder: new NodeFinder()
        );
    }

    /**
     * @param string $code
     * @return Dependency
     */
    public function resolve(string $code): Dependency
    {
        try {
            $ast = $this->parser->parse($code);
        } catch (Error $error) {
            throw new FailedResolveDependencyException($error->getMessage());
        }

        return $this->resolveDependents($this->resolveDepender(new Dependency(), $ast), $ast);
    }

    /**
     * @param Dependency $dependency
     * @param Stmt[] $ast
     * @return Dependency
     */
    protected function resolveDepender(Dependency $dependency, array $ast): Dependency
    {
        foreach ($ast as $node) {
            if ($node instanceof Namespace_) {
                return $this->resolveDepender($dependency->setNamespace($node->name->toString()), $node->stmts);
            }
            if ($node instanceof ClassLike) {
                return $this->resolveDepender($dependency->setDepender($node->name->name), $node->stmts);
            }
        }
        return $dependency;
    }

    /**
     * @param Dependency $dependency
     * @param Stmt[] $ast
     * @return Dependency
     */
    protected function resolveDependents(Dependency $dependency, array $ast): Dependency
    {
        $ast = $this->removeNamespaceName($ast);

        /** @var Node\Name[] $names */
        $names = $this->finder->find($ast, function (Node $node) {
            return $node instanceof Node\Name;
        });
        foreach ($names as $name) {
            $dependency->registerDependent($name->toString());
        }

        return $dependency;
    }

    /**
     * @param array $ast
     * @return Node[]
     */
    protected function removeNamespaceName(array $ast): array
    {
        $this->traverser->addVisitor(new class extends NodeVisitorAbstract {
            public function leaveNode(Node $node)
            {
                if ($node instanceof Namespace_) {
                    $node->name = null;
                }
                return null;
            }
        });
        return $this->traverser->traverse($ast);
    }
}
