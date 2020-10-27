<?php

namespace Jellyfish\CacheSymfony;

use Jellyfish\Cache\CacheInterface;
use Jellyfish\Cache\Exception\InvalidLifeTimeException;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

class Cache implements CacheInterface
{
    /**
     * @var \Symfony\Component\Cache\Adapter\AbstractAdapter
     */
    protected $cacheAdapter;

    /**
     * @param \Symfony\Component\Cache\Adapter\AbstractAdapter $cacheAdapter
     */
    public function __construct(AbstractAdapter $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }


    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key): ?string
    {
        $cacheItem = $this->cacheAdapter->getItem($key);

        if (!$cacheItem->isHit()) {
            return null;
        }

        return $cacheItem->get();
    }

    /**
     * @param string $key
     * @param string $value
     * @param int|null $lifeTime
     *
     * @return \Jellyfish\Cache\CacheInterface
     *
     * @throws \Jellyfish\Cache\Exception\InvalidLifeTimeException
     */
    public function set(string $key, string $value, ?int $lifeTime = null): CacheInterface
    {
        if ($lifeTime !== null && $lifeTime <= 0) {
            throw new InvalidLifeTimeException('The life time value must be greater then zero or null!');
        }

        $cacheItem = $this->cacheAdapter->getItem($key);

        $cacheItem->set($value)
            ->expiresAfter($lifeTime);

        $this->cacheAdapter->save($cacheItem);

        return $this;
    }
}
