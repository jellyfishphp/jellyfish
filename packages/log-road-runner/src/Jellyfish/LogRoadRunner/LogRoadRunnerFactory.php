<?php

declare(strict_types=1);

namespace Jellyfish\LogRoadRunner;

use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Log\LogConstants;
use Psr\Log\LoggerInterface;
use RoadRunner\Logger\Logger as RoadRunnerLogger;
use Spiral\Goridge\RPC\RPC;

class LogRoadRunnerFactory
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface
     */
    protected ConfigFacadeInterface $configFacade;

    /**
     * @var string
     */
    protected string $rootDir;

    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    protected ?LoggerInterface $logger = null;

    /**
     * @param \Jellyfish\Config\ConfigFacadeInterface $configFacade
     * @param string $rootDir
     */
    public function __construct(
        ConfigFacadeInterface $configFacade,
        string $rootDir
    ) {
        $this->configFacade = $configFacade;
        $this->rootDir = $rootDir;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $roadRunnerLogger = new RoadRunnerLogger(RPC::fromGlobals());
            $this->logger = new Logger(
                $roadRunnerLogger,
                $this->configFacade->get(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL)
            );
        }

        return $this->logger;
    }
}
