<?php

declare(strict_types=1);

namespace Jellyfish\Serializer;

use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SerializerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerPropertyNameConverterStrategyProvider($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Serializer\SerializerServiceProvider
     */
    protected function registerPropertyNameConverterStrategyProvider(Container $container): SerializerServiceProvider
    {
        $container->offsetSet(
            SerializerConstants::CONTAINER_KEY_PROPERTY_NAME_CONVERTER_STRATEGY_PROVIDER,
            static function () {
                return new PropertyNameConverterStrategyProvider();
            }
        );

        return $this;
    }
}
