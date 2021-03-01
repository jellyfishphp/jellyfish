<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use ArrayObject;
use Codeception\Test\Unit;
use Jellyfish\Event\Exception\MappingException;
use Jellyfish\Event\Fixtures\Payload;
use Jellyfish\Queue\QueueFacadeInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class EventMapperTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventFactoryMock;

    /**
     * @var \Jellyfish\Queue\QueueFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queueFacadeMock;

    /**
     * @var \Jellyfish\Event\EventMapper
     */
    protected $eventMapper;

    /**
     * @var \Jellyfish\Event\EventInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMock;

    /**
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerFacadeMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventFactoryMock = $this->getMockBuilder(EventFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueFacadeMock = $this->getMockBuilder(QueueFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMapper = new EventMapper($this->eventFactoryMock, $this->queueFacadeMock, $this->serializerFacadeMock);
    }

    /**
     * @return void
     *
     * @throws \Jellyfish\Event\Exception\MappingException
     */
    public function testFromMessageWithArrayBody(): void
    {
        $headers = [
            'body_type' => 'Jellyfish\Event\Fixtures\Payload[]',
            'event_name' => 'test'
        ];
        $body = '[{"name":"Test"}]';

        $payloadItem = new Payload();
        $payloadItem->setName('Test');

        $payload = new ArrayObject();
        $payload->append($payloadItem);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->withConsecutive(['body_type'], ['event_name'])
            ->willReturnOnConsecutiveCalls($headers['body_type'], $headers['event_name']);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('getHeaders')
            ->willReturn($headers);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('getBody')
            ->willReturn($body);

        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('createEvent')
            ->willReturn($this->eventMock);

        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('deserialize')
            ->with($body, $headers['body_type'], 'json')
            ->willReturn($payload);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($headers['event_name'])
            ->willReturn($this->eventMock);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('setPayload')
            ->with($payload)
            ->willReturn($this->eventMock);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('setMetaProperties')
            ->with([])
            ->willReturn($this->eventMock);

        $event = $this->eventMapper->fromMessage($this->messageMock);

        static::assertEquals($this->eventMock, $event);
    }

    /**
     * @return void
     */
    public function testFromInvalidMessage(): void
    {
        $this->messageMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->withConsecutive(['body_type'], ['event_name'])
            ->willReturnOnConsecutiveCalls(null, null);

        $this->messageMock->expects(static::never())
            ->method('getHeaders');

        $this->messageMock->expects(static::never())
            ->method('getBody');

        $this->eventFactoryMock->expects(static::never())
            ->method('createEvent');

        $this->serializerFacadeMock->expects(static::never())
            ->method('deserialize');

        $this->eventMock->expects(static::never())
            ->method('setName');

        $this->eventMock->expects(static::never())
            ->method('setPayload');

        $this->eventMock->expects(static::never())
            ->method('setMetaProperties');

        try {
            $this->eventMapper->fromMessage($this->messageMock);
            static::fail();
        } catch (MappingException $e) {
        }
    }

    /**
     * @return void
     *
     * @throws \Jellyfish\Event\Exception\MappingException
     */
    public function testFromMessage(): void
    {
        $headers = [
            'body_type' => Payload::class,
            'event_name' => 'test'
        ];
        $body = '[{"name":"Test"}]';

        $payload = new Payload();
        $payload->setName('Test');

        $this->messageMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->withConsecutive(['body_type'], ['event_name'])
            ->willReturnOnConsecutiveCalls($headers['body_type'], $headers['event_name']);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('getHeaders')
            ->willReturn($headers);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('getBody')
            ->willReturn($body);

        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('createEvent')
            ->willReturn($this->eventMock);

        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('deserialize')
            ->with($body, $headers['body_type'], 'json')
            ->willReturn($payload);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($headers['event_name'])
            ->willReturn($this->eventMock);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('setPayload')
            ->with($payload)
            ->willReturn($this->eventMock);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('setMetaProperties')
            ->with([])
            ->willReturn($this->eventMock);

        $event = $this->eventMapper->fromMessage($this->messageMock);

        static::assertEquals($this->eventMock, $event);
    }

    /**
     * @return void
     */
    public function testToMessageWithEmptyArrayBody(): void
    {
        $payload = new ArrayObject();
        $eventName = 'test';

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getPayload')
            ->willReturn($payload);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getMetaProperties')
            ->willReturn([]);

        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn('[]');

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('createMessage')
            ->willReturn($this->messageMock);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setHeaders')
            ->with([])
            ->willReturn($this->messageMock);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setHeader')
            ->withConsecutive(['event_name', $eventName], ['body_type', 'ArrayObject'], ['body_type', 'stdClass[]'])
            ->willReturnOnConsecutiveCalls($this->messageMock, $this->messageMock, $this->messageMock);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setBody')
            ->with('[]')
            ->willReturn($this->messageMock);

        $message = $this->eventMapper->toMessage($this->eventMock);

        static::assertEquals($this->messageMock, $message);
    }

    /**
     * @return void
     */
    public function testToMessageWithArrayBody(): void
    {
        $payloadItem = new Payload();
        $payloadItem->setName('Test');

        $payload = new ArrayObject();
        $payload->append($payloadItem);

        $eventName = 'test';
        $body = '[{"name":"Test"}]';

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getPayload')
            ->willReturn($payload);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getMetaProperties')
            ->willReturn([]);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('createMessage')
            ->willReturn($this->messageMock);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setHeaders')
            ->with([])
            ->willReturn($this->messageMock);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setHeader')
            ->withConsecutive(
                ['event_name', $eventName],
                ['body_type', 'ArrayObject'],
                ['body_type', 'Jellyfish\Event\Fixtures\Payload[]']
            )->willReturnOnConsecutiveCalls($this->messageMock, $this->messageMock, $this->messageMock);

        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn($body);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setBody')
            ->with($body)
            ->willReturn($this->messageMock);

        $message = $this->eventMapper->toMessage($this->eventMock);

        static::assertEquals($this->messageMock, $message);
    }

    /**
     * @return void
     */
    public function testToMessage(): void
    {
        $payload = new Payload();
        $payload->setName('Test');

        $eventName = 'test';
        $body = '{"name":"Test"}';

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getPayload')
            ->willReturn($payload);

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getMetaProperties')
            ->willReturn([]);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('createMessage')
            ->willReturn($this->messageMock);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setHeaders')
            ->with([])
            ->willReturn($this->messageMock);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setHeader')
            ->withConsecutive(['event_name', $eventName], ['body_type', Payload::class])
            ->willReturnOnConsecutiveCalls($this->messageMock, $this->messageMock);

        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn($body);

        $this->messageMock->expects(static::atLeastOnce())
            ->method('setBody')
            ->with($body)
            ->willReturn($this->messageMock);

        $message = $this->eventMapper->toMessage($this->eventMock);

        static::assertEquals($this->messageMock, $message);
    }
}
