<?php

namespace Jellyfish\Serializer\NameConverter;

interface PropertyNameConverterStrategyProviderInterface
{
    /**
     * @param string $propertyNameConverterStrategyKey
     * @param \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function addStrategy(
        string $propertyNameConverterStrategyKey,
        PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
    ): PropertyNameConverterStrategyProviderInterface;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function removeStrategy(
        string $propertyNameConverterStrategyKey
    ): PropertyNameConverterStrategyProviderInterface;

    /**
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    public function getAllStrategies(): array;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return bool
     */
    public function hasStrategy(string $propertyNameConverterStrategyKey): bool;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface|null
     */
    public function getStrategy(string $propertyNameConverterStrategyKey): ?PropertyNameConverterStrategyInterface;
}
