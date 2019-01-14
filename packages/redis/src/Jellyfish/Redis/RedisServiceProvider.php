<?php

namespace Jellyfish\Redis;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Predis\Client;

class RedisServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->createRedisClient($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createRedisClient(Container $container): ServiceProviderInterface
    {
        $config = $container->offsetGet('config');

        $container->offsetSet('redis_client', function () use ($config) {
            return new Client([
                'scheme' => 'tcp',
                'host' => $config->get(RedisConstants::REDIS_HOST, RedisConstants::DEFAULT_REDIS_HOST),
                'port' => $config->get(RedisConstants::REDIS_PORT, RedisConstants::DEFAULT_REDIS_PORT),
            ]);
        });

        return $this;
    }
}
