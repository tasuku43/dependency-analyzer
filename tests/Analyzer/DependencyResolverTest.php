<?php
declare(strict_types=1);

namespace Tasuku43\Tests\DependencyChecker\Analyzer;

use PHPUnit\Framework\TestCase;
use Tasuku43\DependencyChecker\Analyzer\DependencyResolver;
use Tasuku43\Tests\DependencyChecker\Analyzer\Data\Package\SampleClass;

class DependencyResolverTest extends TestCase
{
    /**
     * @dataProvider dataProvier
     * @return void
     */
    public function testResolve(string $path): void
    {
        $dependency = DependencyResolver::factory()->resolve(file_get_contents($path));


        var_dump($dependency->getDependentList());
        self::assertContains(SampleClass::class, $dependency->getDependentList());
    }

    public function dataProvier(): array
    {
        return [
            [
                'Call static class method' => __DIR__ . '/Data/CallStaticClassMethod.php'
            ],
            [
                'Call static class const' => __DIR__ . '/Data/CallStaticClassConst.php'
            ],
            [
                'New class' => __DIR__ . '/Data/New_.php'
            ],
            [
                'Return class' => __DIR__ . '/Data/Return_.php'
            ],
            [
                'Method param' => __DIR__ . '/Data/MethodParam.php'
            ],
            [
                'Use' => __DIR__ . '/Data/Use_.php'
            ],
        ];
    }
}
