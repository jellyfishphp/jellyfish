<?php

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Config\ConfigInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\QueueClientInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
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
        $self = $this;

        $pimple->offsetSet('queue_client', function (Container $container) use ($self) {
            return $self->createClient(
                $container->offsetGet('config'),
                $container->offsetGet('message_mapper')
            );
        });
    }

    /**
     * @param \Jellyfish\Config\ConfigInterface $config
     * @param \Jellyfish\Queue\MessageMapperInterface $messageMapper
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     */
    protected function createClient(
        ConfigInterface $config,
        MessageMapperInterface $messageMapper
    ): QueueClientInterface {
        $connection = $this->createConnection($config);

        return new QueueClient($connection, $messageMapper);
    }

    /**
     * @param \Jellyfish\Config\ConfigInterface $config
     *
     * @return \PhpAmqpLib\Connection\AbstractConnection
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     */
    protected function createConnection(ConfigInterface $config): AbstractConnection
    {
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

        $connection = new AMQPStreamConnection(
            $rabbitMqHost,
            $rabbitMqPort,
            $rabbitMqUser,
            $rabbitMqPassword,
            $rabbitMqVhost
        );

        return $connection;
    }
}
