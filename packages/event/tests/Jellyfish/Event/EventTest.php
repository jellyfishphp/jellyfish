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

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->payload = new stdClass();
        $this->uuid = '294452fd-0ba8-481c-8cfd-832a68c2edc3';

        $this->event = new Event($this->uuid);
    }

    /**
     * @return void
     */
    public function testSetAndGetName(): void
    {
        $name = 'test';

        static::assertEquals($this->event, $this->event->setName($name));
        static::assertEquals($name, $this->event->getName());
    }

    /**
     * @return void
     */
    public function testGetMetaProperties(): void
    {
        static::assertCount(0, $this->event->getMetaProperties());
    }

    /**
     * @return void
     */
    public function testSetAndGetMetaProperties(): void
    {
        $metaProperties = [
            'key' => 'value'
        ];

        static::assertEquals($this->event, $this->event->setMetaProperties($metaProperties));
        static::assertEquals($metaProperties, $this->event->getMetaProperties());
    }

    /**
     * @return void
     */
    public function testSetAndGetMetaProperty(): void
    {
        static::assertEquals($this->event, $this->event->setMetaProperty('key', 'value'));
        static::assertEquals('value', $this->event->getMetaProperty('key'));
    }

    /**
     * @return void
     */
    public function testGetMetaProperty(): void
    {
        static::assertEquals(null, $this->event->getMetaProperty('key'));
    }

    /**
     * @return void
     */
    public function testSetAndGetPayload(): void
    {
        static::assertEquals($this->event, $this->event->setPayload($this->payload));
        static::assertEquals($this->payload, $this->event->getPayload());
    }

    /**
     * @return void
     */
    public function testGetId(): void
    {
        static::assertEquals($this->uuid, $this->event->getId());
    }
}
