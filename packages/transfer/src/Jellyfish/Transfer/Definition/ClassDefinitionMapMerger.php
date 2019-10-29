<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

class ClassDefinitionMapMerger implements ClassDefinitionMapMergerInterface
{
    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface[] $classDefinitionMapA
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface[] $classDefinitionMapB
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]
     */
    public function merge(array $classDefinitionMapA, array $classDefinitionMapB): array
    {
        foreach ($classDefinitionMapB as $classDefinitionName => $classDefinition) {
            if (!\array_key_exists($classDefinitionName, $classDefinitionMapA)) {
                $classDefinitionMapA[$classDefinitionName] = $classDefinition;
                continue;
            }

            $this->mergeProperties($classDefinitionMapA[$classDefinitionName], $classDefinition);
        }

        return $classDefinitionMapA;
    }

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinitionA
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinitionB
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface
     */
    protected function mergeProperties(
        ClassDefinitionInterface $classDefinitionA,
        ClassDefinitionInterface $classDefinitionB
    ): ClassDefinitionMapMergerInterface {
        $classPropertyDefinitionMapA = $classDefinitionA->getProperties();

        foreach ($classDefinitionB->getProperties() as $classPropertyDefinitionName => $classPropertyDefinition) {
            if (\array_key_exists($classPropertyDefinitionName, $classPropertyDefinitionMapA)) {
                continue;
            }

            $classPropertyDefinitionMapA[$classPropertyDefinitionName] = $classPropertyDefinition;
        }

        $classDefinitionA->setProperties($classPropertyDefinitionMapA);

        return $this;
    }
}
