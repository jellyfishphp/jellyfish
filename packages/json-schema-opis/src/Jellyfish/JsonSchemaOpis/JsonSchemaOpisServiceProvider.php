<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Jellyfish\JsonSchema\JsonSchemaConstants;
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
        $this->registerJsonSchemaFacade($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\JsonSchemaOpis\JsonSchemaOpisServiceProvider
     */
    protected function registerJsonSchemaFacade(Container $container): JsonSchemaOpisServiceProvider
    {
        $container->offsetSet(JsonSchemaConstants::FACADE, static fn () => new JsonSchemaOpisFacade(
            new JsonSchemaOpisFactory()
        ));

        return $this;
    }
}
