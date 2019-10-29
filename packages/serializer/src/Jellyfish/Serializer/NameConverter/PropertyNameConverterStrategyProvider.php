<?php

declare(strict_types=1);

namespace Jellyfish\Serializer\NameConverter;

class PropertyNameConverterStrategyProvider implements PropertyNameConverterStrategyProviderInterface
{
    /**
     * @var \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    protected $propertyNameConverterStrategyList = [];

    /**
     * @param string $propertyNameConverterStrategyKey
     * @param \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function addStrategy(
        string $propertyNameConverterStrategyKey,
        PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
    ): PropertyNameConverterStrategyProviderInterface {
        $this->propertyNameConverterStrategyList[$propertyNameConverterStrategyKey] = $propertyNameConverterStrategy;

        return $this;
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function removeStrategy(
        string $propertyNameConverterStrategyKey
    ): PropertyNameConverterStrategyProviderInterface {
        if (!$this->hasStrategy($propertyNameConverterStrategyKey)) {
            return $this;
        }

        unset($this->propertyNameConverterStrategyList[$propertyNameConverterStrategyKey]);

        return $this;
    }

    /**
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    public function getAllStrategies(): array
    {
        return $this->propertyNameConverterStrategyList;
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return bool
     */
    public function hasStrategy(string $propertyNameConverterStrategyKey): bool
    {
        return array_key_exists($propertyNameConverterStrategyKey, $this->propertyNameConverterStrategyList);
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface|null
     */
    public function getStrategy(string $propertyNameConverterStrategyKey): ?PropertyNameConverterStrategyInterface
    {
        if (!$this->hasStrategy($propertyNameConverterStrategyKey)) {
            return null;
        }

        return $this->propertyNameConverterStrategyList[$propertyNameConverterStrategyKey];
    }
}
