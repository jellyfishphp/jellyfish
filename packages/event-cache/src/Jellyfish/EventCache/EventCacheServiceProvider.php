<?php

namespace Jellyfish\EventCache;

use Jellyfish\Cache\CacheConstants;
use Jellyfish\Event\EventConstants;
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
        if (!$container->offsetExists(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)) {
            return $this;
        }

        $container->extend(
            EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS,
            static function (array $defaultEventErrorHandlers, Container $container) {
                $defaultEventErrorHandlers[] = new CacheEventErrorHandler(
                    $container->offsetGet(CacheConstants::CONTAINER_KEY_CACHE),
                    $container->offsetGet(SerializerConstants::CONTAINER_KEY_SERIALIZER)
                );

                return $defaultEventErrorHandlers;
            }
        );

        return $this;
    }
}
