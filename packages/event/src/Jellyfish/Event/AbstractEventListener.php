<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Closure;
use Exception;

abstract class AbstractEventListener implements EventListenerInterface
{
    /**
     * @var \Closure|null
     */
    protected $errorHandler;

    /**
     * @param \Closure|null $errorHandler
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    public function setErrorHandler(?Closure $errorHandler): EventListenerInterface
    {
        $this->errorHandler = $errorHandler;

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
        } catch (Exception $e) {
            $this->handleError($e, $event);
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
     * @param \Exception $e
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventListenerInterface
     *
     * @throws \Exception
     */
    protected function handleError(Exception $e, EventInterface $event): EventListenerInterface
    {
        if ($this->errorHandler === null) {
            throw $e;
        }

        $this->errorHandler->call($this, $e, $event);

        return $this;
    }
}
