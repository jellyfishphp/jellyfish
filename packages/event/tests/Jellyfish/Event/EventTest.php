<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use stdClass;

class EventTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventInterface
     */
    protected $event;

    /**
     * @var \stdClass
     */
    protected $payload;

    protected function _before(): void
    {
        parent::_before();

        $this->payload = new stdClass();

        $this->event = new Event();
    }

    /**
     * @return void
     */
    public function testSetAndGetName(): void
    {
        $name = 'test';

        $this->assertEquals($this->event, $this->event->setName($name));
        $this->assertEquals($name, $this->event->getName());
    }

    /**
     * @return void
     */
    public function testSetAndGetRetries(): void
    {
        $retries = 2;

        $this->assertEquals($this->event, $this->event->setRetries($retries));
        $this->assertEquals($retries, $this->event->getRetries());
    }

    /**
     * @return void
     */
    public function testSetAndGetPayload(): void
    {
        $this->assertEquals($this->event, $this->event->setPayload($this->payload));
        $this->assertEquals($this->payload, $this->event->getPayload());
    }
}
