<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Queue\Message;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class MessageMapperTest extends Unit
{
    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerFacadeMock;

    /**
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;

    /**
     * @var string
     */
    protected $json;

    /**
     * @var \Jellyfish\QueueRabbitMq\MessageMapperInterface
     */
    protected $messageMapper;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->json = '{"headeres":{"test":"Test"},"body":"Test"}';

        $this->messageMapper = new MessageMapper($this->serializerFacadeMock);
    }

    /**
     * @return void
     */
    public function testFromJson(): void
    {
        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('deserialize')
            ->with($this->json, Message::class, 'json')
            ->willReturn($this->messageMock);

        static::assertEquals($this->messageMock, $this->messageMapper->fromJson($this->json));
    }

    /**
     * @return void
     */
    public function testToJson(): void
    {
        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->with($this->messageMock, 'json')
            ->willReturn($this->json);

        static::assertEquals($this->json, $this->messageMapper->toJson($this->messageMock));
    }
}
