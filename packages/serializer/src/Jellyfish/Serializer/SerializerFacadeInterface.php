<?php

declare(strict_types=1);

namespace Jellyfish\Serializer;

use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface;

interface SerializerFacadeInterface
{
    /**
     * @param object $data
     * @param string $format
     *
     * @return string
     */
    public function serialize(object $data, string $format): string;

    /**
     * @param string $data
     * @param string $type
     * @param string $format
     *
     * @return object
     */
    public function deserialize(string $data, string $type, string $format): object;

    /**
     * @param string $propertyNameConverterStrategyKey
     * @param \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
     *
     * @return \Jellyfish\Serializer\SerializerFacadeInterface
     */
    public function addPropertyNameConverterStrategy(
        string $propertyNameConverterStrategyKey,
        PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
    ): SerializerFacadeInterface;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\SerializerFacadeInterface
     */
    public function removePropertyNameConverterStrategy(
        string $propertyNameConverterStrategyKey
    ): SerializerFacadeInterface;

    /**
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    public function getAllPropertyNameConverterStrategies(): array;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return bool
     */
    public function hasPropertyNameConverterStrategy(string $propertyNameConverterStrategyKey): bool;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface|null
     */
    public function getPropertyNameConverterStrategy(
        string $propertyNameConverterStrategyKey
    ): ?PropertyNameConverterStrategyInterface;
}
