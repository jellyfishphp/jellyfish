<?php

declare(strict_types=1);

namespace Jellyfish\UuidRamsey;

use Jellyfish\Uuid\UuidConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UuidRamseyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerUuidFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\UuidRamsey\UuidRamseyServiceProvider
     */
    protected function registerUuidFacade(Container $container): UuidRamseyServiceProvider
    {
        $container->offsetSet(UuidConstants::FACADE, static fn () => new UuidRamseyFacade(new UuidRamseyFactory()));

        return $this;
    }
}
