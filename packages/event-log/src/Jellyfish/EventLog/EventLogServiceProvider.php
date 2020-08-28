<?php

namespace Jellyfish\EventLog;

use Jellyfish\Event\EventServiceProvider;
use Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler;
use Jellyfish\Log\LogServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EventLogServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerEventErrorHandler($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\EventLog\EventLogServiceProvider
     */
    protected function registerEventErrorHandler(Container $container): EventLogServiceProvider
    {
        if (!$container->offsetExists(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)) {
            return $this;
        }

        $container->extend(
            EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS,
            static function (array $defaultEventErrorHandlers, Container $container) {
                $logger = $container->offsetGet(LogServiceProvider::CONTAINER_KEY_LOGGER);

                $defaultEventErrorHandlers[] = new LogEventErrorHandler($logger);

                return $defaultEventErrorHandlers;
            }
        );

        return $this;
    }
}
