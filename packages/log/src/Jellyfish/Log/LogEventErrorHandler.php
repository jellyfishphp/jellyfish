<?php

namespace Jellyfish\Log;

use Jellyfish\Event\EventErrorHandlerInterface;
use Jellyfish\Event\EventInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class LogEventErrorHandler implements EventErrorHandlerInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
            'eventListenerIdentifier' => $eventListenerIdentifier,
            'eventName' => $event->getName(),
            'eventMetaProperties' => $event->getMetaProperties(),
            'trace' => $throwable->getTrace()
        ];

        $this->logger->error($throwable->getMessage(), $context);

        return $this;
    }
}
