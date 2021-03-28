<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;

interface DestinationFactoryInterface
{
    /**
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function create(): DestinationInterface;
}
