<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\Error;
use PhpParser\ParserFactory;

class DependencyResolver
{
    public function __construct(private Parser $parser, private NodeTraverserInterface $traverser)
    {
    }

    public static function factory(): self
    {
        return new self(
            parser: (new ParserFactory)->create(ParserFactory::PREFER_PHP7),
            traverser: new NodeTraverser()
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

        $this->traverser->addVisitor(new class extends NodeVisitorAbstract {
            public function leaveNode(Node $node)
            {
                // TODO: Support for parsing dependencies inside classes
                if ($node instanceof Declare_) {
                    return NodeTraverser::REMOVE_NODE;
                }
                return null;
            }
        });

        $ast = $this->traverser->traverse($ast)[0];

        if (!$ast instanceof Namespace_) {
            throw new FailedResolveDependencyException();
        }

        $namespace = implode("\\", $ast->name->parts);

        $dependency = new Dependency();

        foreach ($ast->stmts as $node) {
            if ($node instanceof Class_) {
                $dependency->setDepender($namespace . '\\' . $node->name->name);
            }
            if ($node instanceof Use_) {
                // TODO: Consideration of multiple contents in uses
                $dependency->registerDependent(implode("\\", $node->uses[0]->name->parts));
            }
        }

        return $dependency;
    }
}
