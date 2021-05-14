<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class QueueRabbitMqFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerFacadeMock;

    /**
     * @var \Jellyfish\QueueRabbitMq\QueueRabbitMqFactory
     */
    protected QueueRabbitMqFactory $queueRabbitMqFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueRabbitMqFactory = new QueueRabbitMqFactory(
            $this->configFacadeMock,
            $this->serializerFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testGetQueueClient(): void
    {
        $this->configFacadeMock->expects(static::atLeastOnce())
            ->method('get')
            ->withConsecutive(
                [QueueRabbitMqConstants::RABBIT_MQ_HOST, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_HOST],
                [QueueRabbitMqConstants::RABBIT_MQ_PORT, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PORT],
                [QueueRabbitMqConstants::RABBIT_MQ_USER, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_USER],
                [QueueRabbitMqConstants::RABBIT_MQ_PASSWORD, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PASSWORD],
                [QueueRabbitMqConstants::RABBIT_MQ_VHOST, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_VHOST]
            )->willReturnOnConsecutiveCalls(
                QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_HOST,
                QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PORT,
                QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_USER,
                QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PASSWORD,
                QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_VHOST
            );

        static::assertInstanceOf(
            QueueClient::class,
            $this->queueRabbitMqFactory->getQueueClient()
        );
    }

    /**
     * @return void
     */
    public function testCreateDestination(): void
    {
        static::assertInstanceOf(
            Destination::class,
            $this->queueRabbitMqFactory->createDestination()
        );
    }

    /**
     * @return void
     */
    public function testCreateMessage(): void
    {
        static::assertInstanceOf(
            Message::class,
            $this->queueRabbitMqFactory->createMessage()
        );
    }
}
