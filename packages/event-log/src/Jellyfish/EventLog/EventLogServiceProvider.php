<?php

declare(strict_types = 1);

namespace Jellyfish\EventLog;

use Jellyfish\Event\EventConstants;
use Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler;
use Jellyfish\Log\LogConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @see \Jellyfish\EventLog\EventLogServiceProviderTest
 */
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
        if (!$container->offsetExists(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)) {
            return $this;
        }

        $container->extend(
            EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS,
            static function (array $defaultEventErrorHandlers, Container $container): array {
                $logger = $container->offsetGet(LogConstants::CONTAINER_KEY_LOGGER);

                $defaultEventErrorHandlers[] = new LogEventErrorHandler($logger);

                return $defaultEventErrorHandlers;
            },
        );

        return $this;
    }
}
