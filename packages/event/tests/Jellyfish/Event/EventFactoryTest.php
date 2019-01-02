<?php

namespace Jellyfish\Event;

use Codeception\Test\Unit;

class EventFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventFactoryInterface
     */
    protected $eventFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventFactory = new EventFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $this->assertInstanceOf(Event::class, $this->eventFactory->create());
    }
}
