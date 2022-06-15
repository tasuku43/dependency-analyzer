<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyzer;

class Dependency
{
    private string $namespace;
    private string $depender;
    private array  $dependentList;

    public function __construct()
    {
        $this->namespace = '';
        $this->depender      = 'unknown';
        $this->dependentList = [];
    }

    public function setDepender(string $className): self
    {
        $this->depender = $className;

        return $this;
    }

    public function registerDependent(string $className): self
    {
        foreach ($this->dependentList as $dependent) {
            if (str_ends_with($dependent, $className)) {
                return $this;
            }
        }

        if (!in_array($className, $this->dependentList)) {
            $this->dependentList[] = $className;
        }
        return $this;
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

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getDepender(): string
    {
        return $this->namespace . '\\' . $this->depender;
    }

    /**
     * @return string[]
     */
    public function getDependentList(): array
    {
        return $this->dependentList;
    }
}
