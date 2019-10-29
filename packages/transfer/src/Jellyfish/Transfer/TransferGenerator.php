<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface;
use Jellyfish\Transfer\Generator\FactoryRegistryGeneratorInterface;

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
     * @var \Jellyfish\Transfer\Generator\FactoryRegistryGeneratorInterface
     */
    protected $factoryRegistryGenerator;

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface $classDefinitionMapLoader
     * @param \Jellyfish\Transfer\Generator\FactoryRegistryGeneratorInterface $factoryRegistryGenerator
     * @param \Jellyfish\Transfer\Generator\ClassGeneratorInterface[] $classGenerators
     */
    public function __construct(
        ClassDefinitionMapLoaderInterface $classDefinitionMapLoader,
        FactoryRegistryGeneratorInterface $factoryRegistryGenerator,
        array $classGenerators
    ) {
        $this->classDefinitionMapLoader = $classDefinitionMapLoader;
        $this->factoryRegistryGenerator = $factoryRegistryGenerator;
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

        $this->factoryRegistryGenerator->generate($classDefinitionMap);

        return $this;
    }
}
