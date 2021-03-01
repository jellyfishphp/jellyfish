<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Jellyfish\Config\ConfigConstants;
use Jellyfish\Lock\LockConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LockSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerLockFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\LockSymfony\LockSymfonyServiceProvider
     */
    protected function registerLockFacade(Container $container): LockSymfonyServiceProvider
    {
        $container->offsetSet(LockConstants::FACADE, function (Container $container) {
            $lockFactory = new LockSymfonyFactory(
                $container->offsetGet(ConfigConstants::FACADE)
            );

            return new LockSymfonyFacade($lockFactory);
        });

        return $this;
    }
}
