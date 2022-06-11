<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

use Tasuku43\DependencyChecker\Paser\DependencyParser;

class DependencyAnalyzer
{
    public function __construct(private DependencyParser $parser)
    {
    }

    /**
     * @param string $targetRelativePath
     * @param string $pattern
     * @return Dependency[]
     */
    public function analyze(string $targetRelativePath, string $pattern): array
    {
        $absolutePath = __DIR__ . '/../../' . $targetRelativePath;

        return array_filter(
            $this->dependencyList($absolutePath),
            fn(Dependency $dependency) => str_contains($dependency->getDependent(), $pattern)
        );
    }

    /**
     * @param string $absolutePath
     * @return Dependency[]
     */
    private function dependencyList(string $absolutePath): array
    {
        $files = scandir($absolutePath);
        $files = array_filter($files, function ($file) {
            return !in_array($file, array('.', '..'));
        });

        $dependencyList = [];

        foreach ($files as $file) {
            $fullpath = rtrim($absolutePath, '/') . '/' . $file;
            if (is_file($fullpath)) {
                $dependencyList = [...$dependencyList, ...$this->parser->parse(file_get_contents($fullpath))];
            }
            if (is_dir($fullpath)) {
                $dependencyList = [...$dependencyList, ...$this->dependencyList($fullpath)];
            }
        }

        return $dependencyList;
    }
}
