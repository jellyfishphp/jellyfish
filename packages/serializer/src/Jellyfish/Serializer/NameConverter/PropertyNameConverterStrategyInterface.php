<?php

namespace Jellyfish\Serializer\NameConverter;

interface PropertyNameConverterStrategyInterface
{
    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertAfterNormalize(string $propertyName, ?string $class, ?string $format): ?string;

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertAfterDenormalize(string $propertyName, ?string $class, ?string $format): ?string;
}
