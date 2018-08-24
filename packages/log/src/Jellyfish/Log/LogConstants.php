<?php

namespace Jellyfish\Log;

use Monolog\Logger;

interface LogConstants
{
    const LOG_LEVEL = 'LOG_LEVEL';
    const DEFAULT_LOG_LEVEL = Logger::INFO;
}
