<?php

namespace Jellyfish\Queue;

/**
 * @see \Jellyfish\Queue\DestinationFactoryTest
 */
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
