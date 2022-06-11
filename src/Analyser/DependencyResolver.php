<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

class DependencyResolver
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

    /**
     * @return Dependency[]
     */
    public function resolve(): array
    {
        return array_map(function (string $dependent) {
            return new Dependency($this->depender, $dependent);
        }, $this->dependentList);
    }
}
