<?php

namespace Jellyfish\Queue;

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
