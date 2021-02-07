<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Config\ConfigInterface;
use Jellyfish\Config\ConfigServiceProvider;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\QueueConstants;
use Jellyfish\Queue\QueueServiceProvider;
use Jellyfish\Serializer\SerializerInterface;
use Pimple\Container;

class QueueRabbitMqServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Config\ConfigFacadeInterface
     */
    protected $configFacadeMock;

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

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMapperMock = $this->getMockBuilder(MessageMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->offsetSet(ConfigConstants::FACADE, static function () use ($self) {
            return $self->configFacadeMock;
        });

        $this->container->offsetSet(QueueConstants::CONTAINER_KEY_MESSAGE_MAPPER, static function () use ($self) {
            return $self->messageMapperMock;
        });

        $this->queueRabbitMqServiceProvider = new QueueRabbitMqServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->configFacadeMock->expects(self::atLeastOnce())
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

        self::assertTrue($this->container->offsetExists(QueueRabbitMqConstants::CONTAINER_KEY_CONNECTION));
        self::assertInstanceOf(
            ConnectionInterface::class,
            $this->container->offsetGet(QueueRabbitMqConstants::CONTAINER_KEY_CONNECTION)
        );

        self::assertTrue($this->container->offsetExists(QueueRabbitMqConstants::CONTAINER_KEY_AMQP_MESSAGE_FACTORY));
        self::assertInstanceOf(
            AmqpMessageFactory::class,
            $this->container->offsetGet(QueueRabbitMqConstants::CONTAINER_KEY_AMQP_MESSAGE_FACTORY)
        );

        self::assertTrue($this->container->offsetExists(QueueConstants::CONTAINER_KEY_QUEUE_CLIENT));
        self::assertInstanceOf(
            QueueClient::class,
            $this->container->offsetGet(QueueConstants::CONTAINER_KEY_QUEUE_CLIENT)
        );
    }
}
