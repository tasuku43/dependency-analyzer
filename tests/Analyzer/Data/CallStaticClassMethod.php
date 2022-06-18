<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyAnalyzer\Analyzer\Data;

class CallStaticClassMethod
{
    public function hoge(): void
    {
        \Tasuku43\Tests\DependencyAnalyzer\Analyzer\Data\Package\SampleClass::staticMethod();
    }
}
