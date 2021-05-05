<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony\NameConverter;

class PropertyNameConverter implements PropertyNameConverterInterface
{
    /**
     * @var \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    protected PropertyNameConverterStrategyProviderInterface $strategyProvider;

    /**
     * @param \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface $strategyProvider
     */
    public function __construct(PropertyNameConverterStrategyProviderInterface $strategyProvider)
    {
        $this->strategyProvider = $strategyProvider;
    }

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string
     */
    public function normalize($propertyName, ?string $class = null, ?string $format = null): string
    {
        foreach ($this->strategyProvider->getAll() as $strategy) {
            $convertedPropertyName = $strategy->convertAfterNormalize($propertyName, $class, $format);

            if ($convertedPropertyName !== null) {
                return $convertedPropertyName;
            }
        }

        return $propertyName;
    }

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string
     */
    public function denormalize($propertyName, ?string $class = null, ?string $format = null): string
    {
        foreach ($this->strategyProvider->getAll() as $strategy) {
            $convertedPropertyName = $strategy->convertAfterDenormalize($propertyName, $class, $format);

            if ($convertedPropertyName !== null) {
                return $convertedPropertyName;
            }
        }

        return $propertyName;
    }
}
