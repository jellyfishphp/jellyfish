<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\QueueServiceProvider;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QueueRabbitMqServiceProvider implements ServiceProviderInterface
{
    public const CONTAINER_KEY_CONNECTION = 'queue_rabbit_mq_connection';
    public const CONTAINER_KEY_AMQP_MESSAGE_FACTORY = 'queue_rabbit_mq_amqp_message_factory';

    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerConnection($container)
            ->registerAmqpMessageFactory($container)
            ->registerQueueClient($container);
    }
    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\QueueRabbitMq\QueueRabbitMqServiceProvider
     */
    protected function registerConnection(Container $container): QueueRabbitMqServiceProvider
    {
        $self = $this;

        $container->offsetSet(
            static::CONTAINER_KEY_CONNECTION,
            static function (Container $container) use ($self) {
                $lazyConnection = $self->createAmqpLazyConnection($container);

                return new Connection($lazyConnection);
            }
        );

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\QueueRabbitMq\QueueRabbitMqServiceProvider
     */
    protected function registerAmqpMessageFactory(Container $container): QueueRabbitMqServiceProvider
    {
        $container->offsetSet(static::CONTAINER_KEY_AMQP_MESSAGE_FACTORY, static function () {
            return new AmqpMessageFactory();
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\QueueRabbitMq\QueueRabbitMqServiceProvider
     */
    protected function registerQueueClient(Container $container): QueueRabbitMqServiceProvider
    {
        $self = $this;

        $container->offsetSet(
            QueueServiceProvider::CONTAINER_KEY_QUEUE_CLIENT,
            static function (Container $container) use ($self) {
                return new QueueClient(
                    $self->createConsumers($container),
                    $self->createProducers($container)
                );
            }
        );

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \PhpAmqpLib\Connection\AMQPLazyConnection
     */
    protected function createAmqpLazyConnection(Container $container): AMQPLazyConnection
    {
        $config = $container->offsetGet('config');

        $rabbitMqHost = $config->get(
            QueueRabbitMqConstants::RABBIT_MQ_HOST,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_HOST
        );

        $rabbitMqPort = $config->get(
            QueueRabbitMqConstants::RABBIT_MQ_PORT,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PORT
        );

        $rabbitMqUser = $config->get(
            QueueRabbitMqConstants::RABBIT_MQ_USER,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_USER
        );

        $rabbitMqPassword = $config->get(
            QueueRabbitMqConstants::RABBIT_MQ_PASSWORD,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PASSWORD
        );

        $rabbitMqVhost = $config->get(
            QueueRabbitMqConstants::RABBIT_MQ_VHOST,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_VHOST
        );

        return new AMQPLazyConnection(
            $rabbitMqHost,
            $rabbitMqPort,
            $rabbitMqUser,
            $rabbitMqPassword,
            $rabbitMqVhost
        );
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Queue\ConsumerInterface[]
     */
    protected function createConsumers(Container $container): array
    {
        $connection = $container->offsetGet(static::CONTAINER_KEY_CONNECTION);
        $messageMapper = $container->offsetGet(QueueServiceProvider::CONTAINER_KEY_MESSAGE_MAPPER);

        return [
            DestinationInterface::TYPE_QUEUE => new QueueConsumer($connection, $messageMapper),
            DestinationInterface::TYPE_FANOUT => new FanoutConsumer($connection, $messageMapper)
        ];
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Queue\ProducerInterface[]
     */
    protected function createProducers(Container $container): array
    {
        $connection = $container->offsetGet(static::CONTAINER_KEY_CONNECTION);
        $messageMapper = $container->offsetGet(QueueServiceProvider::CONTAINER_KEY_MESSAGE_MAPPER);
        $amqpMessageFactory = $container->offsetGet(static::CONTAINER_KEY_AMQP_MESSAGE_FACTORY);

        return [
            DestinationInterface::TYPE_QUEUE => new QueueProducer($connection, $messageMapper, $amqpMessageFactory),
            DestinationInterface::TYPE_FANOUT => new FanoutProducer($connection, $messageMapper, $amqpMessageFactory),
        ];
    }
}
