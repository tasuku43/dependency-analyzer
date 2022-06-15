<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyChecker\Analyzer\Data;

class CallStaticClassMethod
{
    public function hoge(): void
    {
        \Tasuku43\Tests\DependencyChecker\Analyzer\Data\Package\SampleClass::staticMethod();
    }
}
