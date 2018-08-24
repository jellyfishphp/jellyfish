<?php

namespace Jellyfish\Log;

use Jellyfish\Config\ConfigInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple
     */
    public function register(Container $pimple)
    {
        $self = $this;

        $pimple['logger'] = function ($container) use ($self) {
            return $self->createLogger($container['config']);
        };
    }

    /**
     * @param ConfigInterface $config
     *
     * @return LoggerInterface
     *
     * @throws \Exception
     */
    protected function createLogger(ConfigInterface $config): LoggerInterface
    {
        $logger = new Logger('jellyfish');

        $logger->pushHandler($this->createStreamHandler($config));

        return $logger;
    }

    /**
     * @param ConfigInterface $config
     * @return \Monolog\Handler\AbstractProcessingHandler
     *
     * @throws \Exception
     */
    protected function createStreamHandler(ConfigInterface $config): AbstractProcessingHandler
    {
        $logLevel = $config->get(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL);

        return new StreamHandler('php://stdout', $logLevel);
    }
}
