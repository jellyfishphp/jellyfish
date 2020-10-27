<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Monolog\Logger;

interface LogConstants
{
    public const CONTAINER_KEY_LOGGER = 'logger';

    public const LOG_LEVEL = 'LOG_LEVEL';
    public const DEFAULT_LOG_LEVEL = Logger::INFO;
}
