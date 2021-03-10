<?php

declare(strict_types=1);

namespace Jellyfish\LogMonolog;

use Jellyfish\Log\LogFacadeInterface;
use Psr\Log\LoggerInterface;

class LogMonologFacade implements LogFacadeInterface
{
    /**
     * @var \Jellyfish\LogMonolog\LogMonologFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\LogMonolog\LogMonologFactory $factory
     */
    public function __construct(LogMonologFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->factory->getLogger();
    }
}
