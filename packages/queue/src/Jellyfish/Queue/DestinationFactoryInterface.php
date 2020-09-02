<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

interface DestinationFactoryInterface
{
    /**
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function create(): DestinationInterface;
}
