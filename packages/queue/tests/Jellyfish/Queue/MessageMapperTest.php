<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

use Codeception\Test\Unit;
use Jellyfish\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class MessageMapperTest extends Unit
{
    protected MessageFactoryInterface&MockObject $messageFactoryMock;

    protected SerializerInterface&MockObject $serializerMock;

    protected MessageInterface&MockObject $messageMock;

    protected string $json;

    protected MessageMapper $messageMapper;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->json = '{"headeres":{"test":"Test"},"body":"Test"}';

        $this->messageMapper = new MessageMapper($this->serializerMock);
    }

    /**
     * @return void
     */
    public function testFromJson(): void
    {
        $this->serializerMock->expects($this->atLeastOnce())
            ->method('deserialize')
            ->with($this->json, Message::class, 'json')
            ->willReturn($this->messageMock);

        $this->assertEquals($this->messageMock, $this->messageMapper->fromJson($this->json));
    }

    /**
     * @return void
     */
    public function testToJson(): void
    {
        $this->serializerMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($this->messageMock, 'json')
            ->willReturn($this->json);

        $this->assertEquals($this->json, $this->messageMapper->toJson($this->messageMock));
    }
}
