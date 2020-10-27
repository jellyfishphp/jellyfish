<?php

namespace Jellyfish\EventCache\EventErrorHandler;

use Jellyfish\Cache\CacheInterface;
use Jellyfish\Event\EventErrorHandlerInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\EventCache\EventCacheConstants;
use Jellyfish\Serializer\SerializerInterface;
use Throwable;

use function sprintf;

class CacheEventErrorHandler implements EventErrorHandlerInterface
{
    /**
     * @var \Jellyfish\Cache\CacheInterface
     */
    protected $cache;

    /**
     * @var \Jellyfish\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Jellyfish\Cache\CacheInterface $cache
     * @param \Jellyfish\Serializer\SerializerInterface $serializer
     */
    public function __construct(
        CacheInterface $cache,
        SerializerInterface $serializer
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer;
    }


    /**
     * @param string $eventListenerIdentifier
     * @param \Jellyfish\Event\EventInterface $event
     * @param \Throwable $throwable
     *
     * @return \Jellyfish\Event\EventErrorHandlerInterface
     */
    public function handle(
        Throwable $throwable,
        string $eventListenerIdentifier,
        EventInterface $event
    ): EventErrorHandlerInterface {
        $this->cache->set(
            $event->getId(),
            $this->serializer->serialize($event, 'json'),
            EventCacheConstants::LIFE_TIME
        );

        return $this;
    }
}
