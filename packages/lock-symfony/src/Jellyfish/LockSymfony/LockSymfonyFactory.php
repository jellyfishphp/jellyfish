<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Lock\LockInterface;
use Predis\Client;
use Symfony\Component\Lock\LockFactory as SymfonyLockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\RedisStore;

class LockSymfonyFactory
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface
     */
    protected $configFacade;

    /**
     * @var \Jellyfish\LockSymfony\LockIdentifierGeneratorInterface
     */
    protected $lockIdentifierGenerator;

    /**
     * @var \Symfony\Component\Lock\LockFactory
     */
    protected $symfonyLockFactory;

    /**
     * @param \Jellyfish\Config\ConfigFacadeInterface $configFacade
     */
    public function __construct(ConfigFacadeInterface $configFacade)
    {
        $this->configFacade = $configFacade;
    }

    /**
     * @param array $identifierParts
     * @param float $ttl
     *
     * @return \Jellyfish\Lock\LockInterface
     */
    public function createLock(array $identifierParts, float $ttl): LockInterface
    {
        $identifier = $this->getLockIdentifierGenerator()->generate($identifierParts);
        $symfonyLock = $this->getSymfonyLockFactory()->createLock($identifier, $ttl, false);

        return new Lock($symfonyLock);
    }

    /**
     * @return \Symfony\Component\Lock\LockFactory
     */
    protected function getSymfonyLockFactory(): SymfonyLockFactory
    {
        if ($this->symfonyLockFactory === null) {
            $redisClient = $this->createRedisClient();
            $redisStore = $this->createRedisStore($redisClient);

            $this->symfonyLockFactory = new SymfonyLockFactory($redisStore);
        }


        return $this->symfonyLockFactory;
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
     * @return \Predis\Client
     */
    protected function createRedisClient(): Client
    {
        return new Client([
            'scheme' => 'tcp',
            'host' => $this->configFacade->get(
                LockSymfonyConstants::REDIS_STORE_HOST,
                LockSymfonyConstants::DEFAULT_REDIS_STORE_HOST
            ), 'port' => $this->configFacade->get(
                LockSymfonyConstants::REDIS_STORE_PORT,
                LockSymfonyConstants::DEFAULT_REDIS_STORE_PORT
            ), 'database' => $this->configFacade->get(
                LockSymfonyConstants::REDIS_STORE_DB,
                LockSymfonyConstants::DEFAULT_REDIS_STORE_DB
            ),
        ]);
    }

    /**
     * @return \Jellyfish\LockSymfony\LockIdentifierGeneratorInterface
     */
    protected function getLockIdentifierGenerator(): LockIdentifierGeneratorInterface
    {
        if ($this->lockIdentifierGenerator === null) {
            $this->lockIdentifierGenerator = new LockIdentifierGenerator();
        }
        return $this->lockIdentifierGenerator;
    }
}
