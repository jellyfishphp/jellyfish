<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use ArrayObject;
use Codeception\Test\Unit;
use Jellyfish\Event\Exception\MappingException;
use Jellyfish\Event\Fixtures\Payload;
use Jellyfish\Queue\MessageFactoryInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Serializer\SerializerInterface;

class EventMapperTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventFactoryMock;

    /**
     * @var \Jellyfish\Queue\MessageFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageFactoryMock;

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
     * @var \Jellyfish\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventFactoryMock = $this->getMockBuilder(EventFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageFactoryMock = $this->getMockBuilder(MessageFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMapper = new EventMapper($this->eventFactoryMock, $this->messageFactoryMock, $this->serializerMock);
    }

    /**
     * @return void
     *
     * @throws \Jellyfish\Event\Exception\MappingException
     */
    public function testFromMessageWithArrayBody(): void
    {
        $eventName = 'test';
        $bodyType = 'Jellyfish\Event\Fixtures\Payload[]';
        $json = '[{"name":"Test"}]';

        $payloadItem = new Payload();
        $payloadItem->setName('Test');

        $payload = new ArrayObject();
        $payload->append($payloadItem);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('getHeader')
            ->withConsecutive(['body_type'], ['event_name'])
            ->willReturnOnConsecutiveCalls($bodyType, $eventName);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn($json);

        $this->eventFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->eventMock);

        $this->serializerMock->expects($this->atLeastOnce())
            ->method('deserialize')
            ->with($json, $bodyType, 'json')
            ->willReturn($payload);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('setName')
            ->with($eventName)
            ->willReturn($this->eventMock);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('setPayload')
            ->with($payload)
            ->willReturn($this->eventMock);

        $event = $this->eventMapper->fromMessage($this->messageMock);

        $this->assertEquals($this->eventMock, $event);
    }

    /**
     * @return void
     */
    public function testFromInvalidMessage(): void
    {
        $eventName = 'test';
        $bodyType = Payload::class;
        $json = '{"name":"Test"}';

        $payload = new Payload();
        $payload->setName('Test');

        $this->messageMock->expects($this->atLeastOnce())
            ->method('getHeader')
            ->withConsecutive(['body_type'], ['event_name'])
            ->willReturnOnConsecutiveCalls(null, null);

        $this->messageMock->expects($this->never())
            ->method('getBody')
            ->willReturn($json);

        $this->eventFactoryMock->expects($this->never())
            ->method('create')
            ->willReturn($this->eventMock);

        $this->serializerMock->expects($this->never())
            ->method('deserialize')
            ->with($json, $bodyType, 'json')
            ->willReturn($payload);

        $this->eventMock->expects($this->never())
            ->method('setName')
            ->with($eventName)
            ->willReturn($this->eventMock);

        $this->eventMock->expects($this->never())
            ->method('setPayload')
            ->with($payload)
            ->willReturn($this->eventMock);

        try {
            $this->eventMapper->fromMessage($this->messageMock);
            $this->fail();
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
        $eventName = 'test';
        $bodyType = Payload::class;
        $json = '{"name":"Test"}';

        $payload = new Payload();
        $payload->setName('Test');

        $this->messageMock->expects($this->atLeastOnce())
            ->method('getHeader')
            ->withConsecutive(['body_type'], ['event_name'])
            ->willReturnOnConsecutiveCalls($bodyType, $eventName);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn($json);

        $this->eventFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->eventMock);

        $this->serializerMock->expects($this->atLeastOnce())
            ->method('deserialize')
            ->with($json, $bodyType, 'json')
            ->willReturn($payload);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('setName')
            ->with($eventName)
            ->willReturn($this->eventMock);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('setPayload')
            ->with($payload)
            ->willReturn($this->eventMock);

        $event = $this->eventMapper->fromMessage($this->messageMock);

        $this->assertEquals($this->eventMock, $event);
    }

    /**
     * @return void
     */
    public function testToMessageWithEmptyArrayBody(): void
    {
        $payload = new ArrayObject();
        $eventName = 'test';

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn($payload);

        $this->serializerMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn('[]');

        $this->messageFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->messageMock);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('setHeader')
            ->withConsecutive(['event_name', $eventName], ['body_type', 'ArrayObject'], ['body_type', 'stdClass[]'])
            ->willReturnOnConsecutiveCalls($this->messageMock, $this->messageMock, $this->messageMock);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('setBody')
            ->with('[]')
            ->willReturn($this->messageMock);

        $message = $this->eventMapper->toMessage($this->eventMock);

        $this->assertEquals($this->messageMock, $message);
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

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn($payload);

        $this->messageFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->messageMock);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('setHeader')
            ->withConsecutive(
                ['event_name', $eventName],
                ['body_type', 'ArrayObject'],
                ['body_type', 'Jellyfish\Event\Fixtures\Payload[]']
            )->willReturnOnConsecutiveCalls($this->messageMock, $this->messageMock, $this->messageMock);

        $this->serializerMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn($body);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('setBody')
            ->with($body)
            ->willReturn($this->messageMock);

        $message = $this->eventMapper->toMessage($this->eventMock);

        $this->assertEquals($this->messageMock, $message);
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

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($eventName);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn($payload);

        $this->messageFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->messageMock);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('setHeader')
            ->withConsecutive(['event_name', $eventName], ['body_type', Payload::class])
            ->willReturnOnConsecutiveCalls($this->messageMock, $this->messageMock);

        $this->serializerMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn($body);

        $this->messageMock->expects($this->atLeastOnce())
            ->method('setBody')
            ->with($body)
            ->willReturn($this->messageMock);

        $message = $this->eventMapper->toMessage($this->eventMock);

        $this->assertEquals($this->messageMock, $message);
    }
}
