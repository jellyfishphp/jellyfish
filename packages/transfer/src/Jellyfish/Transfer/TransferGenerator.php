<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface;

class TransferGenerator implements TransferGeneratorInterface
{
    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface
     */
    protected ClassDefinitionMapLoaderInterface $classDefinitionMapLoader;

    /**
     * @var \Jellyfish\Transfer\Generator\ClassGeneratorInterface[]
     */
    protected array $classGenerators;

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface $classDefinitionMapLoader
     * @param \Jellyfish\Transfer\Generator\ClassGeneratorInterface[] $classGenerators
     */
    public function __construct(
        ClassDefinitionMapLoaderInterface $classDefinitionMapLoader,
        array $classGenerators
    ) {
        $this->classDefinitionMapLoader = $classDefinitionMapLoader;
        $this->classGenerators = $classGenerators;
    }

    /**
     * @return \Jellyfish\Transfer\TransferGeneratorInterface
     */
    public function generate(): TransferGeneratorInterface
    {
        $classDefinitionMap = $this->classDefinitionMapLoader->load();

        foreach ($classDefinitionMap as $classDefinitionMapEntry) {
            foreach ($this->classGenerators as $classGenerator) {
                $classGenerator->generate($classDefinitionMapEntry);
            }
        }

        return $this;
    }
}
