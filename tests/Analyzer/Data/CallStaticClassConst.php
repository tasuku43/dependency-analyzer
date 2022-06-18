<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyAnalyzer\Analyzer\Data;

class CallStaticClassConst
{
    public function hoge(): string
    {
        return \Tasuku43\Tests\DependencyAnalyzer\Analyzer\Data\Package\SampleClass::CONST_;
    }
}
