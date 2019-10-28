<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
        $container->offsetSet('json_schema_validator_factory', function () {
            return new ValidatorFactory();
        });

        return $this;
    }
}
