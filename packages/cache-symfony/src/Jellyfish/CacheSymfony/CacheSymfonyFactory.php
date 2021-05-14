<?php

declare(strict_types=1);

namespace Jellyfish\CacheSymfony;

use Jellyfish\Config\ConfigFacadeInterface;
use Predis\Client;
use Predis\ClientInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheSymfonyFactory
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface
     */
    protected ConfigFacadeInterface $configFacade;

    /**
     * @var \Jellyfish\CacheSymfony\CacheInterface|null
     */
    protected ?CacheInterface $cache = null;

    /**
     * @param \Jellyfish\Config\ConfigFacadeInterface $configFacade
     */
    public function __construct(ConfigFacadeInterface $configFacade)
    {
        $this->configFacade = $configFacade;
    }

    /**
     * @return \Jellyfish\CacheSymfony\CacheInterface
     */
    public function getCache(): CacheInterface
    {
        if ($this->cache === null) {
            $this->cache = new Cache(
                $this->createRedisAdapter()
            );
        }

        return $this->cache;
    }

    /**
     * @return \Symfony\Component\Cache\Adapter\AbstractAdapter
     */
    protected function createRedisAdapter(): AbstractAdapter
    {
        return new RedisAdapter($this->createRedisClient());
    }

    /**
     * @return \Predis\ClientInterface
     */
    protected function createRedisClient(): ClientInterface
    {
        return new Client(
            [
                'scheme' => 'tcp',
                'host' => $this->configFacade->get(
                    CacheSymfonyConstants::REDIS_STORE_HOST,
                    CacheSymfonyConstants::DEFAULT_REDIS_STORE_HOST
                ),
                'port' => $this->configFacade->get(
                    CacheSymfonyConstants::REDIS_STORE_PORT,
                    CacheSymfonyConstants::DEFAULT_REDIS_STORE_PORT
                ),
                'database' => $this->configFacade->get(
                    CacheSymfonyConstants::REDIS_STORE_DB,
                    CacheSymfonyConstants::DEFAULT_REDIS_STORE_DB
                ),
            ]
        );
    }
}
