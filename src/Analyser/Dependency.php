<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

class Dependency
{
    private string $depender;
    private array  $dependentList;

    public function __construct()
    {
        $this->depender      = 'unknown';
        $this->dependentList = [];
    }

    public function setDepender(string $className): void
    {
        $this->depender = $className;
    }

    public function registerDependent(string $className): void
    {
        if (!in_array($className, $this->dependentList)) {
            $this->dependentList[] = $className;
        }
    }

    public function filter(string $pattern): self
    {
        $this->dependentList = array_filter(
            $this->dependentList,
            fn(string $dependent) => str_contains($dependent, $pattern)
        );
        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->dependentList);
    }

    public function getDepender(): string
    {
        return $this->depender;
    }

    /**
     * @return string[]
     */
    public function getDependentList(): array
    {
        return $this->dependentList;
    }
}
