<?php

namespace Jellyfish\Log;

use Monolog\Logger;

interface LogConstants
{
    public const LOG_LEVEL = 'LOG_LEVEL';
    public const DEFAULT_LOG_LEVEL = Logger::INFO;
}
