<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony\NameConverter;

use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface;

use function array_key_exists;

class PropertyNameConverterStrategyProvider implements PropertyNameConverterStrategyProviderInterface
{
    /**
     * @var \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    protected array $propertyNameConverterStrategyList = [];

    /**
     * @param string $propertyNameConverterStrategyKey
     * @param \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
     *
     * @return \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function add(
        string $propertyNameConverterStrategyKey,
        PropertyNameConverterStrategyInterface $propertyNameConverterStrategy
    ): PropertyNameConverterStrategyProviderInterface {
        $this->propertyNameConverterStrategyList[$propertyNameConverterStrategyKey] = $propertyNameConverterStrategy;

        return $this;
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function remove(
        string $propertyNameConverterStrategyKey
    ): PropertyNameConverterStrategyProviderInterface {
        if (!$this->has($propertyNameConverterStrategyKey)) {
            return $this;
        }

        unset($this->propertyNameConverterStrategyList[$propertyNameConverterStrategyKey]);

        return $this;
    }

    /**
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    public function getAll(): array
    {
        return $this->propertyNameConverterStrategyList;
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return bool
     */
    public function has(string $propertyNameConverterStrategyKey): bool
    {
        return array_key_exists($propertyNameConverterStrategyKey, $this->propertyNameConverterStrategyList);
    }

    /**
     * @param string $propertyNameConverterStrategyKey
     *
     * @return \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface|null
     */
    public function get(string $propertyNameConverterStrategyKey): ?PropertyNameConverterStrategyInterface
    {
        if (!$this->has($propertyNameConverterStrategyKey)) {
            return null;
        }

        return $this->propertyNameConverterStrategyList[$propertyNameConverterStrategyKey];
    }
}
