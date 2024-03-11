<?php

declare(strict_types = 1);

namespace Jellyfish\EventLog\EventErrorHandler;

use Jellyfish\Event\EventErrorHandlerInterface;
use Jellyfish\Event\EventInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @see \Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandlerTest
 */
class LogEventErrorHandler implements EventErrorHandlerInterface
{
    protected LoggerInterface $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
        $context = [
            'eventId' => $event->getId(),
            'eventListenerIdentifier' => $eventListenerIdentifier,
            'eventName' => $event->getName(),
            'eventMetaProperties' => $event->getMetaProperties(),
            'trace' => $throwable->getTrace(),
        ];

        $this->logger->error($throwable->getMessage(), $context);

        return $this;
    }
}
