<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

class Dependency
{
    public function __construct(private string $depender, private string $dependent)
    {
    }

    public function getDependent(): string
    {
        return $this->dependent;
    }
}
