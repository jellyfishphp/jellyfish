<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

interface LockSymfonyConstants
{
    public const REDIS_STORE_HOST = 'REDIS_STORE_HOST';
    public const REDIS_STORE_PORT = 'REDIS_STORE_PORT';
    public const REDIS_STORE_DB = 'REDIS_STORE_DB';

    public const DEFAULT_REDIS_STORE_HOST = '127.0.0.1';
    public const DEFAULT_REDIS_STORE_PORT = '6379';
    public const DEFAULT_REDIS_STORE_DB = '0';
}
