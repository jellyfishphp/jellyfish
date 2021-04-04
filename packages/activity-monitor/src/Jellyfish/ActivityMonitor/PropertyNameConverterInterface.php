<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

interface PropertyNameConverterInterface
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
