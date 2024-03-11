<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Jellyfish\Lock\LockIdentifierGeneratorInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Predis\Client;
use Symfony\Component\Lock\LockFactory as SymfonyLockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\RedisStore;

class LockSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerLockFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\LockSymfony\LockSymfonyServiceProvider
     */
    protected function registerLockFactory(Container $container): LockSymfonyServiceProvider
    {
        $self = $this;

        $container->offsetSet('lock_factory', static fn(Container $container): \Jellyfish\LockSymfony\LockFactory => new LockFactory(
            $self->createSymfonyLockFactory($container),
            $self->createLockIdentifierGenerator()
        ));

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Symfony\Component\Lock\LockFactory
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
     * @return \Symfony\Component\Lock\PersistingStoreInterface
     */
    protected function createRedisStore(Client $redisClient): PersistingStoreInterface
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
            'host' => $config->get(
                LockSymfonyConstants::REDIS_STORE_HOST,
                LockSymfonyConstants::DEFAULT_REDIS_STORE_HOST
            ), 'port' => $config->get(
                LockSymfonyConstants::REDIS_STORE_PORT,
                LockSymfonyConstants::DEFAULT_REDIS_STORE_PORT
            ), 'database' => $config->get(
                LockSymfonyConstants::REDIS_STORE_DB,
                LockSymfonyConstants::DEFAULT_REDIS_STORE_DB
            ),
        ]);
    }

    /**
     * @return \Jellyfish\Lock\LockIdentifierGeneratorInterface
     */
    protected function createLockIdentifierGenerator(): LockIdentifierGeneratorInterface
    {
        return new LockIdentifierGenerator();
    }
}
