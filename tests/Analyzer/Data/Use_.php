<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyAnalyzer\Analyzer\Data;

use Tasuku43\Tests\DependencyAnalyzer\Analyzer\Data\Package\SampleClass;

interface Use_
{
    public function exec(): SampleClass;
}
