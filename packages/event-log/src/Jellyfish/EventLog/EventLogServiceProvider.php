<?php

declare(strict_types=1);

namespace Jellyfish\EventLog;

use Jellyfish\Event\EventConstants;
use Jellyfish\Event\EventFacadeInterface;
use Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler;
use Jellyfish\Log\LogConstants;
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
        $container->extend(
            EventConstants::FACADE,
            static function (EventFacadeInterface $eventFacade, Container $container) {
                $eventErrorHandler = new LogEventErrorHandler(
                    $container->offsetGet(LogConstants::FACADE)
                );

                return $eventFacade->addDefaultEventErrorHandler($eventErrorHandler);
            }
        );

        return $this;
    }
}
