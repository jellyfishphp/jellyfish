<?php

declare(strict_types=1);

namespace Jellyfish\Cache;

interface CacheFacadeInterface
{
    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key): ?string;

    /**
     * @param string $key
     * @param string $value
     * @param int|null $lifeTime
     *
     * @return \Jellyfish\Cache\CacheFacadeInterface
     *
     * @throws \Jellyfish\Cache\Exception\InvalidLifeTimeException
     */
    public function set(string $key, string $value, ?int $lifeTime = null): CacheFacadeInterface;
}
