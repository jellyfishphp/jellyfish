<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Jellyfish\Serializer\SerializerFacadeInterface;

use function sprintf;

class ClassDefinitionMapMapper implements ClassDefinitionMapMapperInterface
{
    protected const TYPE = ClassDefinition::class . '[]';
    protected const FORMAT = 'json';

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacade;

    /**
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        SerializerFacadeInterface $serializerFacade
    ) {
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @param string $data
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]
     */
    public function from(string $data): array
    {
        /** @var \Jellyfish\Transfer\Definition\ClassDefinitionInterface[] $classDefinitions */
        $classDefinitions = $this->serializerFacade->deserialize($data, static::TYPE, static::FORMAT);
        $classDefinitionMap = [];

        foreach ($classDefinitions as $classDefinition) {
            $classDefinitionMapKey = $this->generateClassDefinitionMapKey($classDefinition);
            $classDefinitionMap[$classDefinitionMapKey] = $classDefinition;
            $classPropertyDefinitionMap = [];

            foreach ($classDefinition->getProperties() as $classPropertyDefinition) {
                $classPropertyDefinitionMap[$classPropertyDefinition->getName()] = $classPropertyDefinition;
            }

            $classDefinition->setProperties($classPropertyDefinitionMap);
        }

        return $classDefinitionMap;
    }

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return string
     */
    protected function generateClassDefinitionMapKey(ClassDefinitionInterface $classDefinition): string
    {
        if ($classDefinition->getNamespace() === null) {
            return $classDefinition->getName();
        }

        return sprintf('%s\\%s', $classDefinition->getNamespace(), $classDefinition->getName());
    }
}
