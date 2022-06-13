<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyzer;

use Symfony\Component\Finder\Finder;

class DependencyAnalyzer
{
    /**
     * @var string[]
     */
    private array $violations = [];

    public function __construct(private DependencyResolver $resolver)
    {
    }

    /**
     * @param string $targetRelativePath
     * @param string $pattern
     * @return Dependency[]
     */
    public function analyze(string $targetRelativePath, string $pattern): array
    {
        $absolutePath = getcwd() . '/' . $targetRelativePath;

        $dependencyList = [];

        $finder = new Finder();
        // If the pattern string is not present in the file, there is no need to parse dependencies
        $finder->in($absolutePath)->name('*.php')->contains($pattern)->files();

        foreach ($finder as $file) {
            try {
                $dependency = $this->resolver->resolve(file_get_contents($file->getRealPath()))->filter($pattern);
            } catch (FailedResolveDependencyException $exception) {
                $this->violations[] = $exception->getMessage();
                continue;
            }

            if (!$dependency->isEmpty()) {
                $dependencyList[] = $dependency;
            }
        }

        return $dependencyList;
    }

    public function getViolations(): array
    {
        return $this->violations;
    }
}
