<?php

namespace Jellyfish\Log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->createLogger($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     *
     */
    protected function createLogger(Container $container): ServiceProviderInterface
    {
        $self = $this;

        $container->offsetSet('logger', function (Container $container) use ($self) {
            $logger = new Logger('jellyfish');

            $logger->pushHandler($self->createStreamHandler($container));
            $logger->pushHandler($self->createRotatingFileHandler($container));

            return $logger;
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Monolog\Handler\AbstractProcessingHandler
     *
     * @throws \Exception
     */
    protected function createStreamHandler(Container $container): AbstractProcessingHandler
    {
        $logLevel = $container->offsetGet('config')
            ->get(LogConstants::LOG_LEVEL, (string) LogConstants::DEFAULT_LOG_LEVEL);

        return new StreamHandler('php://stdout', (int) $logLevel);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Monolog\Handler\AbstractProcessingHandler
     */
    protected function createRotatingFileHandler(Container $container): AbstractProcessingHandler
    {
        $logLevel = $container->offsetGet('config')
            ->get(LogConstants::LOG_LEVEL, (string) LogConstants::DEFAULT_LOG_LEVEL);

        $filename = $container->offsetGet('root_dir') . 'var/log/jellyfish.log';

        return new RotatingFileHandler($filename, 0, (int) $logLevel);
    }
}
