<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

use Codeception\Test\Unit;
use Jellyfish\Serializer\SerializerFacadeInterface;

class MessageMapperTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\MessageFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageFactoryMock;

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
     * @var \Jellyfish\Queue\MessageMapperInterface
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
        $this->serializerFacadeMock->expects($this->atLeastOnce())
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
        $this->serializerFacadeMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($this->messageMock, 'json')
            ->willReturn($this->json);

        $this->assertEquals($this->json, $this->messageMapper->toJson($this->messageMock));
    }
}
