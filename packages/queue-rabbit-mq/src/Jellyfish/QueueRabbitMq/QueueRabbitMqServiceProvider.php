<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueRabbitMqServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerQueueClient($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\QueueRabbitMq\QueueRabbitMqServiceProvider
     */
    protected function registerQueueClient(Container $container): QueueRabbitMqServiceProvider
    {
        $self = $this;

        $container->offsetSet('queue_client', static function (Container $container) use ($self) {
            return new QueueClient(
                $self->createConnection($container),
                $self->createAmqpMessageFactory(),
                $container->offsetGet('message_mapper')
            );
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \PhpAmqpLib\Connection\AbstractConnection
     */
    protected function createConnection(Container $container): AbstractConnection
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
     * @return \Jellyfish\QueueRabbitMq\AmqpMessageFactoryInterface
     */
    protected function createAmqpMessageFactory(): AmqpMessageFactoryInterface
    {
        return new AmqpMessageFactory();
    }
}
