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
}
