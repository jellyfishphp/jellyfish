<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Jellyfish\Filesystem\FilesystemFacadeInterface;
use SplFileInfo;

use function is_string;

class ClassDefinitionMapLoader implements ClassDefinitionMapLoaderInterface
{
    /**
     * @var \Jellyfish\Transfer\Definition\DefinitionFinderInterface
     */
    protected $definitionFinder;

    /**
     * @var \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    protected $filesystemFacade;

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
     * @param \Jellyfish\Filesystem\FilesystemFacadeInterface $filesystemFacade
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface $classDefinitionMapMapper
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface $classDefinitionMapMerger
     */
    public function __construct(
        DefinitionFinderInterface $definitionFinder,
        FilesystemFacadeInterface $filesystemFacade,
        ClassDefinitionMapMapperInterface $classDefinitionMapMapper,
        ClassDefinitionMapMergerInterface $classDefinitionMapMerger
    ) {
        $this->definitionFinder = $definitionFinder;
        $this->classDefinitionMapMapper = $classDefinitionMapMapper;
        $this->classDefinitionMapMerger = $classDefinitionMapMerger;
        $this->filesystemFacade = $filesystemFacade;
    }

    /**
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]
     */
    public function load(): array
    {
        $classDefinitionMap = [];

        foreach ($this->definitionFinder->find() as $definitionFile) {
            if (!($definitionFile instanceof SplFileInfo) || !is_string($definitionFile->getRealPath())) {
                continue;
            }

            $definitionFileContent = $this->filesystemFacade->readFromFile($definitionFile->getRealPath());

            $currentClassDefinitionMap = $this->classDefinitionMapMapper->from($definitionFileContent);

            $classDefinitionMap = $this->classDefinitionMapMerger
                ->merge($classDefinitionMap, $currentClassDefinitionMap);
        }

        return $classDefinitionMap;
    }
}
