<?php

namespace Jellyfish\Transfer;

use Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface;

class TransferGenerator implements TransferGeneratorInterface
{
    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface
     */
    protected $classDefinitionMapLoader;

    /**
     * @var array
     */
    protected $classGenerators;

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface $classDefinitionMapLoader
     * @param \Jellyfish\Transfer\ClassGenerator\ClassGeneratorInterface[] $classGenerators
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
