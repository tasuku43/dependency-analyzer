<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

class DependencyRelation
{
    public function __construct(private string $depender, private string $dependent)
    {
    }
}
