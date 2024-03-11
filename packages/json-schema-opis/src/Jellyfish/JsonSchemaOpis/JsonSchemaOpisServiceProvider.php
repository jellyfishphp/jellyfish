<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @see \Jellyfish\JsonSchemaOpis\JsonSchemaOpisServiceProviderTest
 */
class JsonSchemaOpisServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerValidatorFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\JsonSchemaOpis\JsonSchemaOpisServiceProvider
     */
    protected function registerValidatorFactory(Container $container): JsonSchemaOpisServiceProvider
    {
        $container->offsetSet('json_schema_validator_factory', static fn(): ValidatorFactory => new ValidatorFactory());

        return $this;
    }
}
