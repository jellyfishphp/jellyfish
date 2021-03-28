<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;

class DestinationFactory implements DestinationFactoryInterface
{
    /**
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function create(): DestinationInterface
    {
        return new Destination();
    }
}
