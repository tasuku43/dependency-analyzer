<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyChecker\Analyzer\Data;

class CallStaticClassConst
{
    public function hoge(): string
    {
        return \Tasuku43\Tests\DependencyChecker\Analyzer\Data\Package\SampleClass::CONST_;
    }
}
