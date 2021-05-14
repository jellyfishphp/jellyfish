<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Queue\QueueFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Jellyfish\Uuid\UuidFacadeInterface;

class EventFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventFactory
     */
    protected EventFactory $eventFactory;

    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processFacadeMock;

    /**
     * @var \Jellyfish\Queue\QueueFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queueFacadeMock;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Uuid\UuidFacadeInterface
     */
    protected $uuidFacadeMock;


    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->processFacadeMock = $this->getMockBuilder(ProcessFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueFacadeMock = $this->getMockBuilder(QueueFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->uuidFacadeMock = $this->getMockBuilder(UuidFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventFactory = new EventFactory(
            $this->processFacadeMock,
            $this->queueFacadeMock,
            $this->serializerFacadeMock,
            $this->uuidFacadeMock,
            DIRECTORY_SEPARATOR
        );
    }

    /**
     * @return void
     */
    public function testCreateEvent(): void
    {
        $this->uuidFacadeMock->expects(static::atLeastOnce())
            ->method('generateUuid')
            ->willReturn('294452fd-0ba8-481c-8cfd-832a68c2edc3');

        static::assertInstanceOf(Event::class, $this->eventFactory->createEvent());
    }

    /**
     * @return void
     */
    public function testGetEventQueueConsumer(): void
    {
        static::assertInstanceOf(EventQueueConsumer::class, $this->eventFactory->getEventQueueConsumer());
    }

    /**
     * @return void
     */
    public function testGetEventListenerProvider(): void
    {
        static::assertInstanceOf(
            EventListenerProvider::class,
            $this->eventFactory->getEventListenerProvider()
        );
    }

    /**
     * @return void
     */
    public function testGetDefaultEventErrorHandlerProvider(): void
    {
        static::assertInstanceOf(
            EventErrorHandlerProvider::class,
            $this->eventFactory->getDefaultEventErrorHandlerProvider()
        );
    }

    /**
     * @return void
     */
    public function testGetEventDispatcher(): void
    {
        static::assertInstanceOf(
            EventDispatcher::class,
            $this->eventFactory->getEventDispatcher()
        );
    }

    /**
     * @return void
     */
    public function testCreateEventQueueWorker(): void
    {
        static::assertInstanceOf(
            EventQueueWorker::class,
            $this->eventFactory->createEventQueueWorker()
        );
    }
}
