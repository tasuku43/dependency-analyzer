<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

use Tasuku43\DependencyChecker\Paser\DependencyParser;

class DependencyAnalyzerFactory
{
    public function __construct(private DependencyParser $parser)
    {
    }

    public function create(string $relativePath): DependencyAnalyzer
    {
        $absolutePath = __DIR__ . '/../../' . $relativePath;

        return new DependencyAnalyzer(...$this->dependencyMaps($absolutePath));
    }

    /**
     * @param string $absolutePath
     * @return DependencyMap[]
     */
    private function dependencyMaps(string $absolutePath): array
    {
        $files = scandir($absolutePath);
        $files = array_filter($files, function ($file) {
            return !in_array($file, array('.', '..'));
        });

        $dependencies = [];

        foreach ($files as $file) {
            $fullpath = rtrim($absolutePath, '/') . '/' . $file;
            if (is_file($fullpath)) {
                $dependencies[] = $this->parser->parse(file_get_contents($fullpath));
            }
            if (is_dir($fullpath)) {
                $dependencies = array_merge($dependencies, $this->dependencyMaps($fullpath));
            }
        }

        return $dependencies;
    }
}
