<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Psr\Log\LogLevel;

interface LogConstants
{
    public const LOG_LEVEL_EMERGENCY = LogLevel::EMERGENCY;
    public const LOG_LEVEL_ALERT = LogLevel::EMERGENCY;
    public const LOG_LEVEL_CRITICAL = LogLevel::CRITICAL;
    public const LOG_LEVEL_ERROR = LogLevel::ERROR;
    public const LOG_LEVEL_WARNING = LogLevel::WARNING;
    public const LOG_LEVEL_NOTICE = LogLevel::NOTICE;
    public const LOG_LEVEL_INFO = LogLevel::INFO;
    public const LOG_LEVEL_DEBUG = LogLevel::DEBUG;

    public const LOG_LEVEL = 'LOG_LEVEL';
    public const DEFAULT_LOG_LEVEL = self::LOG_LEVEL_INFO;

    public const FACADE = 'facade_log';

    public const LOGGER_NAME = 'jellyfish';
}
