<?php

declare(strict_types = 1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\QueueConstants;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use Pimple\Container;

class QueueRabbitMqServiceProviderTest extends Unit
{
    protected MockObject&ConfigInterface $configMock;

    protected MockObject&MessageMapperInterface $messageMapperMock;

    protected Container $container;

    protected QueueRabbitMqServiceProvider $queueRabbitMqServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $self = $this;

        $this->container = new Container();

        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMapperMock = $this->getMockBuilder(MessageMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->offsetSet(ConfigConstants::CONTAINER_KEY_CONFIG, static fn (): MockObject&ConfigInterface => $self->configMock);

        $this->container->offsetSet(QueueConstants::CONTAINER_KEY_MESSAGE_MAPPER, static fn (): MockObject&MessageMapperInterface => $self->messageMapperMock);

        $this->queueRabbitMqServiceProvider = new QueueRabbitMqServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->configMock->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnCallback(static fn (string $key, ?string $default = null): LogicException|string => match([$key, $default]) {
                [QueueRabbitMqConstants::RABBIT_MQ_HOST, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_HOST] => QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_HOST,
                [QueueRabbitMqConstants::RABBIT_MQ_PORT, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PORT] => QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PORT,
                [QueueRabbitMqConstants::RABBIT_MQ_USER, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_USER] => QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_USER,
                [QueueRabbitMqConstants::RABBIT_MQ_PASSWORD, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PASSWORD] => QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PASSWORD,
                [QueueRabbitMqConstants::RABBIT_MQ_VHOST, QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_VHOST] => QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_VHOST,
                default => new LogicException('Unsupported Parameter')
            });

        $this->queueRabbitMqServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists(QueueRabbitMqConstants::CONTAINER_KEY_CONNECTION));
        $this->assertInstanceOf(ConnectionInterface::class, $this->container->offsetGet(QueueRabbitMqConstants::CONTAINER_KEY_CONNECTION));

        $this->assertTrue($this->container->offsetExists(QueueRabbitMqConstants::CONTAINER_KEY_AMQP_MESSAGE_FACTORY));
        $this->assertInstanceOf(AmqpMessageFactory::class, $this->container->offsetGet(QueueRabbitMqConstants::CONTAINER_KEY_AMQP_MESSAGE_FACTORY));

        $this->assertTrue($this->container->offsetExists(QueueConstants::CONTAINER_KEY_QUEUE_CLIENT));
        $this->assertInstanceOf(QueueClient::class, $this->container->offsetGet(QueueConstants::CONTAINER_KEY_QUEUE_CLIENT));
    }
}
