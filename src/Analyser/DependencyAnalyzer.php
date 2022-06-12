<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

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

        foreach ((new Finder())->in($absolutePath)->name('*.php')->files() as $file) {
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
