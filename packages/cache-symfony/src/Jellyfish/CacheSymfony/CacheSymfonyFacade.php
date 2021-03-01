<?php

declare(strict_types=1);

namespace Jellyfish\CacheSymfony;

use Jellyfish\Cache\CacheFacadeInterface;

class CacheSymfonyFacade implements CacheFacadeInterface
{
    /**
     * @var \Jellyfish\CacheSymfony\CacheSymfonyFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\CacheSymfony\CacheSymfonyFactory $factory
     */
    public function __construct(CacheSymfonyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key): ?string
    {
        return $this->factory->getCache()->get($key);
    }

    /**
     * @param string $key
     * @param string $value
     * @param int|null $lifeTime
     *
     * @return \Jellyfish\Cache\CacheFacadeInterface
     *
     * @throws \Jellyfish\Cache\Exception\InvalidLifeTimeException
     */
    public function set(string $key, string $value, ?int $lifeTime = null): CacheFacadeInterface
    {
        $this->factory->getCache()->set($key, $value, $lifeTime);

        return $this;
    }
}
