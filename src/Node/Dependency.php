<?php
declare(strict_types=1);


namespace Tasuku43\DependencyChecker\Node;


class Dependency
{
    private array $dependencies;

    public function __construct(private string $className)
    {
    }

    public function register(string $className): void
    {
        if (!in_array($className, $this->dependencies)) {
            $this->dependencies[] = $className;
        }
    }
}
