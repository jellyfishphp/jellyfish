<?php

namespace Jellyfish\SerializerSymfony\NameConverter;

use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface;

class PropertyNameConverter implements PropertyNameConverterInterface
{
    /**
     * @var \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    protected $strategyProvider;

    /**
     * @param \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface $strategyProvider
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
        foreach ($this->strategyProvider->getAllStrategies() as $strategy) {
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
        foreach ($this->strategyProvider->getAllStrategies() as $strategy) {
            $convertedPropertyName = $strategy->convertAfterDenormalize($propertyName, $class, $format);

            if ($convertedPropertyName !== null) {
                return $convertedPropertyName;
            }
        }

        return $propertyName;
    }
}
