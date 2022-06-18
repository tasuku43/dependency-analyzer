<?php
declare(strict_types=1);

namespace Tasuku43\DependencyAnalyzer\Analyzer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class DependencyAnalyzer
{
    /**
     * @var string[]
     */
    private array $violations = [];

    public function __construct(
        private DependencyResolver $resolver,
        private OutputInterface $output
    )
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

        $progressBar = new ProgressBar($this->output, $finder->count());
        $progressBar->setFormat(' [%bar%] %percent:3s%%');
        foreach ($finder as $file) {
            $progressBar->advance();
            try {
                $dependency = $this->resolver->resolve($file->getContents())->filter($pattern);
            } catch (FailedResolveDependencyException $exception) {
                $this->violations[] = $exception->getMessage();
                continue;
            }

            if (!$dependency->isEmpty()) {
                $dependencyList[] = $dependency;
            }
        }
        $progressBar->finish();
        $this->output->writeln('');

        return $dependencyList;
    }

    public function getViolations(): array
    {
        return $this->violations;
    }
}
