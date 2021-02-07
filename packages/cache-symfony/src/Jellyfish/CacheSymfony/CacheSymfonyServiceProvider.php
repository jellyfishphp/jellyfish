<?php

namespace Jellyfish\CacheSymfony;

use Jellyfish\Cache\CacheConstants;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Predis\Client;
use Predis\ClientInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     */
    public function register(Container $container): void
    {
        $this->registerCacheFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\CacheSymfony\CacheSymfonyServiceProvider
     */
    protected function registerCacheFacade(Container $container): CacheSymfonyServiceProvider
    {
        $container->offsetSet(CacheConstants::FACADE, static function (Container $container) {
            $cacheSymfonyFactory = new CacheSymfonyFactory(
                $container->offsetGet(ConfigConstants::FACADE)
            );

            return new CacheSymfonyFacade($cacheSymfonyFactory);
        });

        return $this;
    }
}
