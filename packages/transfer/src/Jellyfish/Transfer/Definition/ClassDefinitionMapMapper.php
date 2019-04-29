<?php

namespace Jellyfish\Transfer\Definition;

use Jellyfish\Serializer\SerializerInterface;

class ClassDefinitionMapMapper implements ClassDefinitionMapMapperInterface
{
    protected const TYPE = ClassDefinition::class . '[]';
    protected const FORMAT = 'json';

    /**
     * @var \Jellyfish\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Jellyfish\Serializer\SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param string $data
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]
     */
    public function from(string $data): array
    {
        /** @var \Jellyfish\Transfer\Definition\ClassDefinitionInterface[] $jsonClassDefinitions */
        $jsonClassDefinitions = $this->serializer->deserialize($data, static::TYPE, static::FORMAT);
        $jsonClassDefinitionMap = [];

        foreach ($jsonClassDefinitions as $jsonClassDefinition) {
            $jsonClassDefinitionMap[$jsonClassDefinition->getName()] = $jsonClassDefinition;
            $jsonClassPropertyDefinitionMap = [];

            foreach ($jsonClassDefinition->getProperties() as $jsonClassPropertyDefinition) {
                $jsonClassPropertyDefinitionMap[$jsonClassPropertyDefinition->getName()] = $jsonClassPropertyDefinition;
            }

            $jsonClassDefinition->setProperties($jsonClassPropertyDefinitionMap);
        }

        return $jsonClassDefinitionMap;
    }
}
