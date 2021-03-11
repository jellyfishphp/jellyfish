<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Psr\Log\LogLevel;

interface LogConstants
{
    public const LOG_LEVEL = 'LOG_LEVEL';
    public const DEFAULT_LOG_LEVEL = LogLevel::INFO;

    public const FACADE = 'facade_log';

    public const LOGGER_NAME = 'LOGGER_NAME';
    public const DEFAULT_LOGGER_NAME = 'jellyfish';
}
