<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Event\Exception\NotSupportedMethodException;
use Jellyfish\Event\Exception\NotSupportedTypeException;
use Throwable;

use function get_class;
use function sprintf;

abstract class AbstractEventBulkListener extends AbstractEventListener implements EventBulkListenerInterface
{
    /**
     * @param \Jellyfish\Event\EventInterface[] $events
     *
     * @return \Jellyfish\Event\EventBulkListenerInterface
     *
     * @throws \Jellyfish\Event\Exception\NotSupportedTypeException
     */
    public function handleBulk(array $events): EventBulkListenerInterface
    {
        if ($this->getType() === self::TYPE_SYNC) {
            throw new NotSupportedTypeException(
                sprintf('Event listeners that extend from "%s" only support type "%s"', __CLASS__, self::TYPE_ASYNC)
            );
        }

        foreach ($events as $event) {
            try {
                $this->doHandle($event);
            } catch (Throwable $e) {
                $this->handleError($e, $event);
            }
        }

        return $this;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     * @return \Jellyfish\Event\EventListenerInterface
     *
     * @throws \Exception
     */
    public function handle(EventInterface $event): EventListenerInterface
    {
        throw new NotSupportedMethodException(
            sprintf('Method "handle" is not supported by "%s"', get_class($this))
        );
    }
}
