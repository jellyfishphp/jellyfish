<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LogFactory
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = new Logger(LogConstants::LOGGER_NAME);
        }

        return $this->logger;
    }
}
