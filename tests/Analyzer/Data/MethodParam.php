<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyAnalyzer\Analyzer\Data;

interface MethodParam
{
    public function exec(\Tasuku43\Tests\DependencyAnalyzer\Analyzer\Data\Package\SampleClass $class);
}
