<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyzer;

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

        $dependency = $this->buildByAst(new Dependency(), $ast);

        $this->traverser->addVisitor($this->removeNamespaceNameVisitor());
        $ast = $this->traverser->traverse($ast);

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
     * @param Dependency $dependency
     * @param Stmt[] $ast
     * @return Dependency
     */
    protected function buildByAst(Dependency $dependency, array $ast): Dependency
    {
        foreach ($ast as $node) {
            if ($node instanceof Namespace_) {
                $namespace = implode("\\", $node->name->parts);
                return $this->buildByAst($dependency->setNamespace($namespace), $node->stmts);
            }
            if ($node instanceof ClassLike) {
                return $this->buildByAst($dependency->setDepender($node->name->name), $node->stmts);
            }
        }
        return $dependency;
    }

    private function removeNamespaceNameVisitor(): NodeVisitorAbstract
    {
        return new class extends NodeVisitorAbstract {
            public function leaveNode(Node $node)
            {
                if ($node instanceof Namespace_) {
                    $node->name = null;
                }
                return null;
            }
        };
    }
}
