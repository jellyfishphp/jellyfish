<?php

namespace Jellyfish\LockSymfony;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Predis\Client;
use Symfony\Component\Lock\Factory as SymfonyLockFactory;
use Symfony\Component\Lock\Store\RedisStore;
use Symfony\Component\Lock\StoreInterface;

class LockSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->createLockFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createLockFactory(Container $container): ServiceProviderInterface
    {
        $self = $this;

        $container->offsetSet('lock_factory', function (Container $container) use ($self) {
            return new LockFactory($self->createSymfonyLockFactory($container));
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Symfony\Component\Lock\Factory
     */
    protected function createSymfonyLockFactory(Container $container): SymfonyLockFactory
    {
        $redisClient = $this->createRedisClient($container);
        $redisStore = $this->createRedisStore($redisClient);

        return new SymfonyLockFactory($redisStore);
    }

    /**
     * @param \Predis\Client $redisClient
     *
     * @return \Symfony\Component\Lock\StoreInterface
     */
    protected function createRedisStore(Client $redisClient): StoreInterface
    {
        return new RedisStore($redisClient);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Predis\Client
     */
    protected function createRedisClient(Container $container): Client
    {
        $config = $container->offsetGet('config');

        return new Client([
            'scheme' => 'tcp',
            'host' => $config->get(LockConstants::REDIS_STORE_HOST, LockConstants::DEFAULT_REDIS_STORE_HOST),
            'port' => $config->get(LockConstants::REDIS_STORE_PORT, LockConstants::DEFAULT_REDIS_STORE_PORT),
            'database' => $config->get(LockConstants::REDIS_STORE_DB, LockConstants::DEFAULT_REDIS_STORE_DB),
        ]);
    }
}
