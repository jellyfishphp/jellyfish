<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Psr\Log\LoggerInterface;

interface LogFacadeInterface
{
    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface;
}
