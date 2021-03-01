<?php

declare(strict_types=1);

namespace Jellyfish\EventCache;

use Jellyfish\Cache\CacheConstants;
use Jellyfish\Event\EventConstants;
use Jellyfish\Event\EventFacadeInterface;
use Jellyfish\EventCache\EventErrorHandler\CacheEventErrorHandler;
use Jellyfish\Serializer\SerializerConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EventCacheServiceProvider implements ServiceProviderInterface
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
     * @return \Jellyfish\EventCache\EventCacheServiceProvider
     */
    protected function registerEventErrorHandler(Container $container): EventCacheServiceProvider
    {
        $container->extend(
            EventConstants::FACADE,
            static function (EventFacadeInterface $eventFacade, Container $container) {
                $eventErrorHandler = new CacheEventErrorHandler(
                    $container->offsetGet(CacheConstants::FACADE),
                    $container->offsetGet(SerializerConstants::FACADE)
                );

                return $eventFacade->addDefaultEventErrorHandler($eventErrorHandler);
            }
        );

        return $this;
    }
}
