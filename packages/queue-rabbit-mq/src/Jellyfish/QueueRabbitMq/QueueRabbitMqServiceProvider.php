<?php

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Config\ConfigInterface;
use Jellyfish\Queue\ClientInterface;
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

        $pimple->offsetSet('queue_client', function ($container) use ($self) {
            return $self->createClient($container['config']);
        });
    }


    /**
     * @param \Jellyfish\Config\ConfigInterface $config
     *
     * @return \Jellyfish\Queue\ClientInterface
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     */
    protected function createClient(ConfigInterface $config): ClientInterface
    {
        $connection = $this->createConnection($config);

        $client = new Client($connection);

        return $client;
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
            $rabbitMqUser,
            $rabbitMqPort,
            $rabbitMqPassword,
            $rabbitMqVhost
        );

        return $connection;
    }
}
