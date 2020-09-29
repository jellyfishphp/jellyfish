<?php

namespace Jellyfish\Queue;

use Codeception\Test\Unit;

class DestinationFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\DestinationFactory
     */
    protected $destinationFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->destinationFactory = new DestinationFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $destination = $this->destinationFactory->create(DestinationInterface::TYPE_FANOUT);

        self::assertInstanceOf(Destination::class, $destination);
    }
}
