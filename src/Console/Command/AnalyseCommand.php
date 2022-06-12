<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tasuku43\DependencyChecker\Analyser\DependencyAnalyzer;
use Tasuku43\DependencyChecker\Paser\DependencyResolver;

class  AnalyseCommand extends Command
{
    protected function configure()
    {
        $this->setName('analyse')
            ->setDescription('Dependency check command')
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'Relative directory path to be analyzed.'
            )->addOption(
                'pattern',
                null,
                InputOption::VALUE_REQUIRED,
                'Fuga'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path      = $input->getOption('path');
        $pattern = $input->getOption('pattern');

        $analyzer = new DependencyAnalyzer(DependencyResolver::factory());


        $symfonyStyle = new SymfonyStyle($input, $output);

        $results = [];
        foreach ($analyzer->analyze($path, $pattern) as $dependency) {
            $results[$dependency->getDepender()][] = $dependency->getDependent();
        }

        foreach ($results as $depender => $dependentList) {
            $header = ['Depender', $depender];

            $rows = [['Dependent List', array_shift($dependentList)]];
            $rows = [...$rows, ...array_map(function ($dependent) {
                return ['', $dependent];
            }, $dependentList)];

            $symfonyStyle->table($header, $rows);
        }

        return self::SUCCESS;
    }
}
