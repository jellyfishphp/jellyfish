<?php

declare(strict_types = 1);

namespace Jellyfish\Queue;

use Codeception\Test\Unit;

class DestinationFactoryTest extends Unit
{
    protected DestinationFactory $destinationFactory;

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
        $destination = $this->destinationFactory->create();

        $this->assertInstanceOf(Destination::class, $destination);
    }
}
