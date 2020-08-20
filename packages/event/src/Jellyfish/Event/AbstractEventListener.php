<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Throwable;

abstract class AbstractEventListener implements EventListenerInterface
{
    /**
     * @var \Jellyfish\Event\EventErrorHandlerInterface[]
     */
    protected $errorHandlers;

    /**
     * @return \Jellyfish\Event\EventErrorHandlerInterface[]
     */
    public function getErrorHandlers(): array
    {
        return $this->errorHandlers;
    }

    /**
     * @param \Jellyfish\Event\EventErrorHandlerInterface[] $errorHandlers
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    public function setErrorHandlers(array $errorHandlers): EventListenerInterface
    {
        $this->errorHandlers = $errorHandlers;

        return $this;
    }

    /**
     * @param \Jellyfish\Event\EventErrorHandlerInterface $errorHandler
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    public function addErrorHandler(EventErrorHandlerInterface $errorHandler): EventListenerInterface
    {
        $this->errorHandlers[] = $errorHandler;

        return $this;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventListenerInterface
     *
     * @throws \Exception
     */
    public function handle(EventInterface $event): EventListenerInterface
    {
        try {
            $this->doHandle($event);
        } catch (Throwable $error) {
            $this->handleError($error, $event);
        }

        return $this;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    abstract protected function doHandle(EventInterface $event): EventListenerInterface;

    /**
     * @param \Throwable $error
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventListenerInterface
     *
     * @throws \Throwable
     */
    protected function handleError(Throwable $error, EventInterface $event): EventListenerInterface
    {
        if ($this->errorHandlers === null || count($this->errorHandlers) === 0) {
            throw $error;
        }

        foreach ($this->errorHandlers as $errorHandler) {
            $errorHandler->handle($error, $this->getIdentifier(), $event);
        }

        return $this;
    }
}
