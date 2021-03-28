<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationInterface;

class DestinationTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\DestinationInterface
     */
    protected $destination;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->destination = new Destination();
    }

    /**
     * @return void
     */
    public function testSetAndGetName(): void
    {
        $name = 'Foo';

        $this->destination->setName($name);
        self::assertEquals($name, $this->destination->getName());
    }

    /**
     * @return void
     */
    public function testSetAndGetType(): void
    {
        $this->destination->setType(DestinationInterface::TYPE_FANOUT);
        self::assertEquals(DestinationInterface::TYPE_FANOUT, $this->destination->getType());
    }

    /**
     * @return void
     */
    public function testSetAndGetProperty(): void
    {
        $propertyName = 'bind';
        $propertyValue = 'QueueX';

        $this->destination->setProperty($propertyName, $propertyValue);
        self::assertEquals($propertyValue, $this->destination->getProperty($propertyName));
    }

    /**
     * @return void
     */
    public function testGetNonExistingHeader(): void
    {
        $propertyName = 'bind';

        self::assertEquals(null, $this->destination->getProperty($propertyName));
    }
}
