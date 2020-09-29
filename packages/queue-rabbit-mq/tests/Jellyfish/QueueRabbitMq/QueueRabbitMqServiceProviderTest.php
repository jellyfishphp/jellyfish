<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigInterface;
use Jellyfish\Config\ConfigServiceProvider;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\QueueServiceProvider;
use Jellyfish\Serializer\SerializerInterface;
use Pimple\Container;

class QueueRabbitMqServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Config\ConfigInterface
     */
    protected $configMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\MessageMapperInterface
     */
    protected $messageMapperMock;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\QueueRabbitMq\QueueRabbitMqServiceProvider
     */
    protected $queueRabbitMqServiceProvider;

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

        $this->container->offsetSet(ConfigServiceProvider::CONTAINER_KEY_CONFIG, static function () use ($self) {
            return $self->configMock;
        });

        $this->container->offsetSet(QueueServiceProvider::CONTAINER_KEY_MESSAGE_MAPPER, static function () use ($self) {
            return $self->messageMapperMock;
        });

        $this->queueRabbitMqServiceProvider = new QueueRabbitMqServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->configMock->expects(self::atLeastOnce())
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

        $this->queueRabbitMqServiceProvider->register($this->container);

        self::assertTrue($this->container->offsetExists(QueueRabbitMqServiceProvider::CONTAINER_KEY_CONNECTION));
        self::assertInstanceOf(
            ConnectionInterface::class,
            $this->container->offsetGet(QueueRabbitMqServiceProvider::CONTAINER_KEY_CONNECTION)
        );

        self::assertTrue($this->container->offsetExists(QueueRabbitMqServiceProvider::CONTAINER_KEY_AMQP_MESSAGE_FACTORY));
        self::assertInstanceOf(
            AmqpMessageFactory::class,
            $this->container->offsetGet(QueueRabbitMqServiceProvider::CONTAINER_KEY_AMQP_MESSAGE_FACTORY)
        );

        self::assertTrue($this->container->offsetExists(QueueServiceProvider::CONTAINER_KEY_QUEUE_CLIENT));
        self::assertInstanceOf(
            QueueClient::class,
            $this->container->offsetGet(QueueServiceProvider::CONTAINER_KEY_QUEUE_CLIENT)
        );
    }
}
