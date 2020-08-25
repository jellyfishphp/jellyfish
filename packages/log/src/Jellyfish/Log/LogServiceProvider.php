<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Jellyfish\Event\EventServiceProvider;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public const KEY_LOGGER = 'logger';

    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerLogger($container)
            ->registerEventErrorHandler($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Log\LogServiceProvider
     */
    protected function registerLogger(Container $container): LogServiceProvider
    {
        $self = $this;

        $container->offsetSet(static::KEY_LOGGER, static function (Container $container) use ($self) {
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

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Log\LogServiceProvider
     */
    protected function registerEventErrorHandler(Container $container): LogServiceProvider
    {
        if (!$container->offsetExists(EventServiceProvider::KEY_DEFAULT_EVENT_ERROR_HANDLERS)) {
            return $this;
        }

        $container->extend(
            EventServiceProvider::KEY_DEFAULT_EVENT_ERROR_HANDLERS,
            static function (array $defaultEventErrorHandlers, Container $container) {
                $defaultEventErrorHandlers[] = new LogEventErrorHandler($container->offsetGet(static::KEY_LOGGER));

                return $defaultEventErrorHandlers;
            }
        );

        return $this;
    }
}
