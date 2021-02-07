<?php

namespace Jellyfish\EventCache\EventErrorHandler;

use Jellyfish\Cache\CacheFacadeInterface;
use Jellyfish\Event\EventErrorHandlerInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\EventCache\EventCacheConstants;
use Jellyfish\Serializer\SerializerInterface;
use Throwable;

class CacheEventErrorHandler implements EventErrorHandlerInterface
{
    /**
     * @var \Jellyfish\Cache\CacheFacadeInterface
     */
    protected $cacheFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Jellyfish\Cache\CacheFacadeInterface $cacheFacade
     * @param \Jellyfish\Serializer\SerializerInterface $serializer
     */
    public function __construct(
        CacheFacadeInterface $cacheFacade,
        SerializerInterface $serializer
    ) {
        $this->cacheFacade = $cacheFacade;
        $this->serializer = $serializer;
    }


    /**
     * @param \Throwable $throwable
     * @param string $eventListenerIdentifier
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventErrorHandlerInterface
     */
    public function handle(
        Throwable $throwable,
        string $eventListenerIdentifier,
        EventInterface $event
    ): EventErrorHandlerInterface {
        $this->cacheFacade->set(
            $event->getId(),
            $this->serializer->serialize($event, 'json'),
            EventCacheConstants::LIFE_TIME
        );

        return $this;
    }
}
