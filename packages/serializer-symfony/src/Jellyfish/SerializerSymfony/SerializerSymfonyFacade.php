<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class SerializerSymfonyFacade implements SerializerFacadeInterface
{
    /**
     * @var \Jellyfish\SerializerSymfony\SerializerSymfonyFactory
     */
    protected SerializerSymfonyFactory $factory;

    /**
     * @param \Jellyfish\SerializerSymfony\SerializerSymfonyFactory $factory
     */
    public function __construct(SerializerSymfonyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param object $data
     * @param string $format
     *
     * @return string
     */
    public function serialize(object $data, string $format): string
    {
        return $this->factory->getSerializer()->serialize($data, $format);
    }

    /**
     * @param string $data
     * @param string $type
     * @param string $format
     *
     * @return object
     */
    public function deserialize(string $data, string $type, string $format): object
    {
        return $this->factory->getSerializer()->deserialize($data, $type, $format);
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     * @param \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
     *
     * @return \Jellyfish\Serializer\SerializerFacadeInterface
     */
    public function addPropertyNameConverterStrategy(
        string $propertyNameConverterStrategyKey,
        PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
    ): SerializerFacadeInterface {
        $this->factory->getPropertyNameConverterStrategyProvider()
            ->add($propertyNameConverterStrategyKey, $propertyNameConverterStrategy);

        return $this;
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\SerializerFacadeInterface
     */
    public function removePropertyNameConverterStrategy(
        string $propertyNameConverterStrategyKey
    ): SerializerFacadeInterface {
        $this->factory->getPropertyNameConverterStrategyProvider()
            ->remove($propertyNameConverterStrategyKey);

        return $this;
    }

    /**
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    public function getAllPropertyNameConverterStrategies(): array
    {
        return $this->factory->getPropertyNameConverterStrategyProvider()
            ->getAll();
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return bool
     */
    public function hasPropertyNameConverterStrategy(string $propertyNameConverterStrategyKey): bool
    {
        return $this->factory->getPropertyNameConverterStrategyProvider()
            ->has($propertyNameConverterStrategyKey);
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface|null
     */
    public function getPropertyNameConverterStrategy(
        string $propertyNameConverterStrategyKey
    ): ?PropertyNameConverterStrategyInterface {
        return $this->factory->getPropertyNameConverterStrategyProvider()
            ->get($propertyNameConverterStrategyKey);
    }
}
