<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony\NameConverter;

use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface;

interface PropertyNameConverterStrategyProviderInterface
{
    /**
     * @param string $propertyNameConverterStrategyKey
     * @param \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
     *
     * @return \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function add(
        string $propertyNameConverterStrategyKey,
        PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
    ): PropertyNameConverterStrategyProviderInterface;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function remove(
        string $propertyNameConverterStrategyKey
    ): PropertyNameConverterStrategyProviderInterface;

    /**
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    public function getAll(): array;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return bool
     */
    public function has(string $propertyNameConverterStrategyKey): bool;

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface|null
     */
    public function get(string $propertyNameConverterStrategyKey): ?PropertyNameConverterStrategyInterface;
}
