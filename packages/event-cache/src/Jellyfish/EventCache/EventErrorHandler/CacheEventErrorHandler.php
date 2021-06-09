<?php

declare(strict_types=1);

namespace Jellyfish\EventCache\EventErrorHandler;

use Jellyfish\Cache\CacheFacadeInterface;
use Jellyfish\Event\EventErrorHandlerInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\EventCache\EventCacheConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Throwable;

class CacheEventErrorHandler implements EventErrorHandlerInterface
{
    /**
     * @var \Jellyfish\Cache\CacheFacadeInterface
     */
    protected CacheFacadeInterface $cacheFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected SerializerFacadeInterface $serializerFacade;

    /**
     * @param \Jellyfish\Cache\CacheFacadeInterface $cacheFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        CacheFacadeInterface $cacheFacade,
        SerializerFacadeInterface $serializerFacade
    ) {
        $this->cacheFacade = $cacheFacade;
        $this->serializerFacade = $serializerFacade;
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
            $this->serializerFacade->serialize($event, 'json'),
            EventCacheConstants::LIFE_TIME
        );

        return $this;
    }
}
