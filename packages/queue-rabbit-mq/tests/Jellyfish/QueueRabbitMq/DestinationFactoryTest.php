<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;

class DestinationFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\QueueRabbitMq\DestinationFactory
     */
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
        static::assertInstanceOf(
            Destination::class,
            $this->destinationFactory->create()
        );
    }
}
