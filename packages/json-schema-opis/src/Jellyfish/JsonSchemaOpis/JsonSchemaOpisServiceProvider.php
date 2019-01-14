<?php

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
        $this->createValidatorFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createValidatorFactory(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('json_schema_validator_factory', function () {
            return new ValidatorFactory();
        });

        return $this;
    }
}
