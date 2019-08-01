<?php

namespace Jellyfish\Transfer\Definition;

use Jellyfish\Filesystem\FilesystemInterface;
use SplFileInfo;

class ClassDefinitionMapLoader implements ClassDefinitionMapLoaderInterface
{
    /**
     * @var \Jellyfish\Transfer\Definition\DefinitionFinderInterface
     */
    protected $definitionFinder;

    /**
     * @var \Jellyfish\Filesystem\FilesystemInterface
     */
    protected $filesystem;

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
     * @param \Jellyfish\Filesystem\FilesystemInterface $filesystem
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface $classDefinitionMapMapper
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface $classDefinitionMapMerger
     */
    public function __construct(
        DefinitionFinderInterface $definitionFinder,
        FilesystemInterface $filesystem,
        ClassDefinitionMapMapperInterface $classDefinitionMapMapper,
        ClassDefinitionMapMergerInterface $classDefinitionMapMerger
    ) {
        $this->definitionFinder = $definitionFinder;
        $this->classDefinitionMapMapper = $classDefinitionMapMapper;
        $this->classDefinitionMapMerger = $classDefinitionMapMerger;
        $this->filesystem = $filesystem;
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

            $definitionFileContent = $this->filesystem->readFromFile($definitionFile->getRealPath());

            $currentClassDefinitionMap = $this->classDefinitionMapMapper->from($definitionFileContent);

            $classDefinitionMap = $this->classDefinitionMapMerger
                ->merge($classDefinitionMap, $currentClassDefinitionMap);
        }

        return $classDefinitionMap;
    }
}
