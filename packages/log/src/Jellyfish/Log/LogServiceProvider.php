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
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $self = $this;

        $pimple->offsetSet('logger', function ($container) use ($self) {
            return $self->createLogger($container['config']);
        });
    }

    /**
     * @param \Jellyfish\Config\ConfigInterface $config
     *
     * @return \Psr\Log\LoggerInterface
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
     * @param \Jellyfish\Config\ConfigInterface $config
     *
     * @return \Monolog\Handler\AbstractProcessingHandler
     *
     * @throws \Exception
     */
    protected function createStreamHandler(ConfigInterface $config): AbstractProcessingHandler
    {
        $logLevel = $config->get(LogConstants::LOG_LEVEL, (string) LogConstants::DEFAULT_LOG_LEVEL);

        return new StreamHandler('php://stdout', (int) $logLevel);
    }
}
