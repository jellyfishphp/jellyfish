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
        $this->registerCache($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\CacheSymfony\CacheSymfonyServiceProvider
     */
    protected function registerCache(Container $container): CacheSymfonyServiceProvider
    {
        $self = $this;

        $container->offsetSet(CacheConstants::CONTAINER_KEY_CACHE, static function (Container $container) use ($self) {
            return new Cache($self->createRedisAdapter($container));
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Symfony\Component\Cache\Adapter\AbstractAdapter
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     */
    protected function createRedisAdapter(Container $container): AbstractAdapter
    {
        $redisClient = $this->createRedisClient($container);

        return new RedisAdapter($redisClient, 'cache');
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Predis\ClientInterface
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     */
    protected function createRedisClient(Container $container): ClientInterface
    {
        /** @var \Jellyfish\Config\ConfigInterface $config */
        $config = $container->offsetGet(ConfigConstants::CONTAINER_KEY_CONFIG);

        return new Client([
            'scheme' => 'tcp',
            'host' => $config->get(
                CacheSymfonyConstants::REDIS_STORE_HOST,
                CacheSymfonyConstants::DEFAULT_REDIS_STORE_HOST
            ), 'port' => $config->get(
                CacheSymfonyConstants::REDIS_STORE_PORT,
                CacheSymfonyConstants::DEFAULT_REDIS_STORE_PORT
            ), 'database' => $config->get(
                CacheSymfonyConstants::REDIS_STORE_DB,
                CacheSymfonyConstants::DEFAULT_REDIS_STORE_DB
            ),
        ]);
    }
}
