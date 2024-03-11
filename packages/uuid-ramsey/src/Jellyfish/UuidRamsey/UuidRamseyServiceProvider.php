<?php

namespace Jellyfish\UuidRamsey;

use Jellyfish\Uuid\UuidConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Ramsey\Uuid\UuidFactory;

/**
 * @see \Jellyfish\UuidRamsey\UuidRamseyServiceProviderTest
 */
class UuidRamseyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerUuidGenerator($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\UuidRamsey\UuidRamseyServiceProvider
     */
    protected function registerUuidGenerator(Container $container): UuidRamseyServiceProvider
    {
        $container->offsetSet(UuidConstants::CONTAINER_KEY_UUID_GENERATOR, static fn(): UuidGenerator => new UuidGenerator(new UuidFactory()));

        return $this;
    }
}
