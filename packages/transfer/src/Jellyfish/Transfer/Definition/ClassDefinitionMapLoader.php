<?php

declare(strict_types = 1);

namespace Jellyfish\Transfer\Definition;

use Jellyfish\Filesystem\FilesystemInterface;
use SplFileInfo;

/**
 * @see \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderTest
 */
class ClassDefinitionMapLoader implements ClassDefinitionMapLoaderInterface
{
    protected DefinitionFinderInterface $definitionFinder;

    protected FilesystemInterface $filesystem;

    protected ClassDefinitionMapMapperInterface $classDefinitionMapMapper;

    protected ClassDefinitionMapMergerInterface $classDefinitionMapMerger;

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
            if (!($definitionFile instanceof SplFileInfo)) {
                continue;
            }

            if (!\is_string($definitionFile->getRealPath())) {
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
