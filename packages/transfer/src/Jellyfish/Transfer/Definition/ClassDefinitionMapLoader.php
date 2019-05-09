<?php

namespace Jellyfish\Transfer\Definition;

use SplFileInfo;

class ClassDefinitionMapLoader implements ClassDefinitionMapLoaderInterface
{
    /**
     * @var \Jellyfish\Transfer\Definition\DefinitionFinderInterface
     */
    protected $definitionFinder;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface
     */
    protected $classDefinitionMapMapper;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface
     */
    protected $classDefinitionMapMerger;

    /**
     * @param \Jellyfish\Transfer\Definition\DefinitionFinderInterface $definitionFinder
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface $classDefinitionMapMapper
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface $classDefinitionMapMerger
     */
    public function __construct(
        DefinitionFinderInterface $definitionFinder,
        ClassDefinitionMapMapperInterface $classDefinitionMapMapper,
        ClassDefinitionMapMergerInterface $classDefinitionMapMerger
    ) {
        $this->definitionFinder = $definitionFinder;
        $this->classDefinitionMapMapper = $classDefinitionMapMapper;
        $this->classDefinitionMapMerger = $classDefinitionMapMerger;
    }

    /**
     * @return \Jellyfish\Transfer\Definition\ClassDefinition[]
     */
    public function load(): array
    {
        $classDefinitionMap = [];

        foreach ($this->definitionFinder->find() as $definitionFile) {
            if (!($definitionFile instanceof SplFileInfo)) {
                continue;
            }

            $definitionFileContent = \file_get_contents($definitionFile->getRealPath());

            $currentClassDefinitionMap = $this->classDefinitionMapMapper->from($definitionFileContent);

            $classDefinitionMap = $this->classDefinitionMapMerger
                ->merge($classDefinitionMap, $currentClassDefinitionMap);
        }

        return $classDefinitionMap;
    }
}
