<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Queue\MessageInterface;

interface EventMapperInterface
{
    /**
     * @param \Jellyfish\Event\EventInterface $event
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function toMessage(EventInterface $event): MessageInterface;

    /**
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function fromMessage(MessageInterface $message): EventInterface;
}
