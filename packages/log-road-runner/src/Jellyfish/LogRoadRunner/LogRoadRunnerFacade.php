<?php

declare(strict_types=1);

namespace Jellyfish\LogRoadRunner;

use Jellyfish\Log\LogFacadeInterface;
use Jellyfish\LogRoadRunner\LogRoadRunnerFactory;
use Psr\Log\LoggerInterface;

class LogRoadRunnerFacade implements LogFacadeInterface
{
    /**
     * @var \Jellyfish\LogRoadRunner\LogRoadRunnerFactory
     */
    protected LogRoadRunnerFactory $factory;

    /**
     * @param \Jellyfish\LogRoadRunner\LogRoadRunnerFactory $factory
     */
    public function __construct(LogRoadRunnerFactory $factory)
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
