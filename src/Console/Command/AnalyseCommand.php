<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tasuku43\DependencyChecker\Analyser\Dependency;
use Tasuku43\DependencyChecker\Analyser\DependencyAnalyzer;
use Tasuku43\DependencyChecker\Analyser\DependencyResolver;

class AnalyseCommand extends Command
{
    private const GROUP_BY_DEPENDER  = 'depender';
    private const GROUP_BY_DEPENDENT = 'dependent';

    protected function configure()
    {
        $this->setName('analyse')
            ->setDescription('Dependency check command')
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'Relative directory path to be analyzed'
            )->addOption(
                'pattern',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify namespace or class name'
            )->addOption(
                'group-by',
                null,
                InputOption::VALUE_OPTIONAL,
                'Grouping Option ("depender" or "dependent")',
                self::GROUP_BY_DEPENDER
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $path    = $input->getOption('path');
        $pattern = $input->getOption('pattern');
        $groupBy = $input->getOption('group-by');

        if (!in_array($groupBy, [self::GROUP_BY_DEPENDER, self::GROUP_BY_DEPENDENT])) {
            $symfonyStyle->error([
                'Invalid value for group-by option',
                sprintf('Please specify `%s` or `%s`', self::GROUP_BY_DEPENDER, self::GROUP_BY_DEPENDENT)
            ]);

            return self::FAILURE;
        }

        $analyzer      = new DependencyAnalyzer(DependencyResolver::factory());
        $dependecyList = $analyzer->analyze($path, $pattern);

        return match ($groupBy) {
            self::GROUP_BY_DEPENDER  => $this->reportGroupByDepender($dependecyList, $symfonyStyle),
            self::GROUP_BY_DEPENDENT => $this->reportGroupByDependent($dependecyList, $symfonyStyle),
        };
    }

    /**
     * @param Dependency[] $dependecyList
     * @param SymfonyStyle $symfonyStyle
     * @return int
     */
    protected function reportGroupByDepender(array $dependecyList, SymfonyStyle $symfonyStyle): int
    {
        foreach ($dependecyList as $dependency) {
            $header = ['Depender', $dependency->getDepender()];

            $dependentList = $dependency->getDependentList();

            $rows = [['Dependent List', array_shift($dependentList)]];
            $rows = [...$rows, ...array_map(function ($dependent) {
                return ['', $dependent];
            }, $dependentList)];

            $symfonyStyle->table($header, $rows);
        }

        $symfonyStyle->success(sprintf('Found %s dependers', count($dependecyList)));

        return self::SUCCESS;
    }

    /**
     * @param Dependency[] $dependecyList
     * @param SymfonyStyle $symfonyStyle
     * @return int
     */
    protected function reportGroupByDependent(array $dependecyList, SymfonyStyle $symfonyStyle): int
    {
        $dependent2depender = [];
        foreach ($dependecyList as $dependency) {
            foreach ($dependency->getDependentList() as $dependent) {
                $dependent2depender[$dependent][] = $dependency->getDepender();
            }
        }

        foreach ($dependent2depender as $dependent => $dependerList) {
            $header = ['Dependent', $dependent];

            $rows = [['Depender List', array_shift($dependerList)]];
            $rows = [...$rows, ...array_map(function ($depender) {
                return ['', $depender];
            }, $dependerList)];

            $symfonyStyle->table($header, $rows);
        }

        $symfonyStyle->success(sprintf('Found %s dependents', count($dependent2depender)));

        return self::SUCCESS;
    }
}
