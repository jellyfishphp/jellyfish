<?php

declare(strict_types=1);

namespace Jellyfish\EventLog\EventErrorHandler;

use Jellyfish\Event\EventErrorHandlerInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\Log\LogFacadeInterface;
use Throwable;

class LogEventErrorHandler implements EventErrorHandlerInterface
{
    /**
     * @var \Jellyfish\Log\LogFacadeInterface
     */
    protected $logFacade;

    /**
     * @param \Jellyfish\Log\LogFacadeInterface $logFacade
     */
    public function __construct(LogFacadeInterface $logFacade)
    {
        $this->logFacade = $logFacade;
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
            'trace' => $throwable->getTrace()
        ];

        $this->logFacade->getLogger()->error($throwable->getMessage(), $context);

        return $this;
    }
}
