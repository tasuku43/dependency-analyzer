<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

class DependencyMap
{
    private string $depender;

    /**
     * @var array<int, string>
     */
    private array $dependentList;

    public function __construct(private string $namespace)
    {
        $this->depender      = 'unknown';
        $this->dependentList = [];
    }

    public function setDepender(string $depender): void
    {
        $this->depender = $depender;
    }

    public function getDependerFullName(): string
    {
        return $this->namespace . '\\' . $this->depender;
    }

    public function registerDependent(string $className): void
    {
        if (!in_array($className, $this->dependentList)) {
            $this->dependentList[] = $className;
        }
    }

    /**
     * @param string $namespaceName
     * @return string[]
     */
    public function getDependentListFilteredBy(string $namespaceName): array
    {
        return array_filter(
            $this->dependentList,
            fn(string $dependent) => str_starts_with($dependent, $namespaceName)
        );
    }
}
