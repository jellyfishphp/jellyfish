<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use function is_string;
use function lcfirst;
use function preg_replace;
use function preg_replace_callback;
use function strpos;
use function strtolower;
use function strtoupper;

class PropertyNameConverter implements PropertyNameConverterInterface
{
    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertAfterNormalize(string $propertyName, ?string $class, ?string $format): ?string
    {
        if (!$this->canConvert($class)) {
            return null;
        }

        $convertedPropertyName = preg_replace('/[A-Z]/', '_\\0', lcfirst($propertyName));

        if (!is_string($convertedPropertyName)) {
            return null;
        }

        return strtolower($convertedPropertyName);
    }

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertAfterDenormalize(string $propertyName, ?string $class, ?string $format): ?string
    {
        if (!$this->canConvert($class)) {
            return null;
        }

        $camelCasedName = preg_replace_callback('/(^|_|\.)+(.)/', static function (array $match) {
            return ('.' === $match[1] ? '_' : '') . strtoupper($match[2]);
        }, $propertyName);

        if (!is_string($camelCasedName)) {
            return null;
        }

        return lcfirst($camelCasedName);
    }

    /**
     * @param string|null $class
     *
     * @return bool
     */
    protected function canConvert(?string $class): bool
    {
        if ($class === null) {
            return false;
        }

        return strpos($class, 'Generated\\Transfer\\Pm2\\') === 0;
    }
}
