<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyChecker\Analyzer\Data;

interface MethodParam
{
    public function exec(\Tasuku43\Tests\DependencyChecker\Analyzer\Data\Package\SampleClass $class);
}
