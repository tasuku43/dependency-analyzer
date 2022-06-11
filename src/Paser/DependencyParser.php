<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Paser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Tasuku43\DependencyChecker\Analyser\Dependency;
use Tasuku43\DependencyChecker\Analyser\DependencyResolver;

class DependencyParser
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
     * @return Dependency[]
     */
    public function parse(string $code): array
    {
        $ast = $this->parser->parse($code);

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
        assert($ast instanceof Namespace_);

        $namespace = implode("\\", $ast->name->parts);

        $dependencyResolver = new DependencyResolver();

        foreach ($ast->stmts as $node) {
            if ($node instanceof Class_) {
                $dependencyResolver->setDepender($namespace . '\\' . $node->name->name);
            }
            if ($node instanceof Use_) {
                // TODO: Consideration of multiple contents in uses
                $dependencyResolver->registerDependent(implode("\\", $node->uses[0]->name->parts));
            }
        }

        return $dependencyResolver->resolve();
    }
}
