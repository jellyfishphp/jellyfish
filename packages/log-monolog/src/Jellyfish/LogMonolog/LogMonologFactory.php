<?php

declare(strict_types=1);

namespace Jellyfish\LogMonolog;

use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Log\LogConstants;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LogMonologFactory
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface
     */
    protected $configFacade;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = new Logger(
                $this->configFacade->get(LogConstants::LOGGER_NAME, LogConstants::DEFAULT_LOGGER_NAME),
                $this->createDefaultHandlers()
            );
        }

        return $this->logger;
    }

    /**
     * @return \Monolog\Handler\HandlerInterface[]
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    public function createDefaultHandlers(): array
    {
        return [
            $this->createStreamHandler(),
            $this->createRotatingFileHandler()
        ];
    }

    /**
     * @return \Monolog\Handler\AbstractProcessingHandler
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    protected function createStreamHandler(): HandlerInterface
    {
        return new StreamHandler(
            'php://stdout',
            $this->configFacade->get(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL)
        );
    }

    /**
     * @return \Monolog\Handler\AbstractProcessingHandler
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    protected function createRotatingFileHandler(): HandlerInterface
    {
        return new RotatingFileHandler(
            sprintf('%svar/log/jellyfish.log', $this->rootDir),
            10,
            $this->configFacade->get(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL)
        );
    }
}
