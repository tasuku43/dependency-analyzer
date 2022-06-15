<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyChecker\Analyzer\Data;

class New_
{
    public function newSampleClass(): void
    {
        $class = new \Tasuku43\Tests\DependencyChecker\Analyzer\Data\Package\SampleClass();
    }
}
