<?php
declare(strict_types=1);

namespace Tasuku43\DependencyChecker\Analyser;

class DependencyAnalyzer
{
    /**
     * @var DependencyMap[]
     */
    private array $dependencyMaps;

    public function __construct(DependencyMap ...$dependencyMaps)
    {
        $this->dependencyMaps = $dependencyMaps;
    }

    /**
     * @param string $namespaceName
     * @return DependencyRelation[]
     */
    public function dependencyRelations(string $namespaceName): array
    {
        $relations = [];

         foreach ($this->dependencyMaps as $dependencyMap) {
             $temp = array_map(function (string $dependent) use ($dependencyMap) {
                 return new DependencyRelation(
                     depender: $dependencyMap->getDependerFullName(),
                     dependent: $dependent
                 );
             }, $dependencyMap->getDependentListFilteredBy($namespaceName));

             if ($temp !== []) {
                 $relations = array_merge($relations, $temp);
             }
         }

         return $relations;
    }
}
