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

        $this->event = new Event('294452fd-0ba8-481c-8cfd-832a68c2edc3');
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
    public function testGetMetaProperties(): void
    {
        $this->assertCount(0, $this->event->getMetaProperties());
    }

    /**
     * @return void
     */
    public function testSetAndGetMetaProperties(): void
    {
        $metaProperties = [
            'key' => 'value'
        ];

        $this->assertEquals($this->event, $this->event->setMetaProperties($metaProperties));
        $this->assertEquals($metaProperties, $this->event->getMetaProperties());
    }

    /**
     * @return void
     */
    public function testSetAndGetMetaProperty(): void
    {
        $this->assertEquals($this->event, $this->event->setMetaProperty('key', 'value'));
        $this->assertEquals('value', $this->event->getMetaProperty('key'));
    }

    /**
     * @return void
     */
    public function testGetMetaProperty(): void
    {
        $this->assertEquals(null, $this->event->getMetaProperty('key'));
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
