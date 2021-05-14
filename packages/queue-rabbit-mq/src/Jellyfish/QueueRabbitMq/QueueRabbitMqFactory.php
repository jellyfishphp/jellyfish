<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;
use PhpAmqpLib\Connection\AMQPLazyConnection;

class QueueRabbitMqFactory
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface
     */
    protected ConfigFacadeInterface $configFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected SerializerFacadeInterface $serializerFacade;

    /**
     * @var \Jellyfish\QueueRabbitMq\Connection|null
     */
    protected ?Connection $connection = null;

    /**
     * @var \Jellyfish\QueueRabbitMq\QueueClientInterface|null
     */
    protected ?QueueClientInterface $queueClient = null;

    /**
     * @var \Jellyfish\QueueRabbitMq\DestinationFactoryInterface|null
     */
    protected ?DestinationFactoryInterface $destinationFactory = null;

    /**
     * @param \Jellyfish\Config\ConfigFacadeInterface $configFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        ConfigFacadeInterface $configFacade,
        SerializerFacadeInterface $serializerFacade
    ) {
        $this->configFacade = $configFacade;
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function createDestination(): DestinationInterface
    {
        return $this->getDestinationFactory()->create();
    }

    /**
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function createMessage(): MessageInterface
    {
        return new Message();
    }

    /**
     * @return \Jellyfish\QueueRabbitMq\QueueClientInterface
     */
    public function getQueueClient(): QueueClientInterface
    {
        if ($this->queueClient === null) {
            $this->queueClient = new QueueClient(
                $this->createConsumers(),
                $this->createProducers()
            );
        }

        return $this->queueClient;
    }

    /**
     * @return \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    protected function getConnection(): ConnectionInterface
    {
        if ($this->connection === null) {
            $this->connection = new Connection($this->createAmqpLazyConnection());
        }

        return $this->connection;
    }

    /**
     * @return \PhpAmqpLib\Connection\AMQPLazyConnection
     */
    protected function createAmqpLazyConnection(): AMQPLazyConnection
    {
        $rabbitMqHost = $this->configFacade->get(
            QueueRabbitMqConstants::RABBIT_MQ_HOST,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_HOST
        );

        $rabbitMqPort = $this->configFacade->get(
            QueueRabbitMqConstants::RABBIT_MQ_PORT,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PORT
        );

        $rabbitMqUser = $this->configFacade->get(
            QueueRabbitMqConstants::RABBIT_MQ_USER,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_USER
        );

        $rabbitMqPassword = $this->configFacade->get(
            QueueRabbitMqConstants::RABBIT_MQ_PASSWORD,
            QueueRabbitMqConstants::DEFAULT_RABBIT_MQ_PASSWORD
        );

        $rabbitMqVhost = $this->configFacade->get(
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
     * @return \Jellyfish\QueueRabbitMq\ConsumerInterface[]
     */
    protected function createConsumers(): array
    {
        $connection = $this->getConnection();
        $messageMapper = new MessageMapper($this->serializerFacade);

        return [
            DestinationInterface::TYPE_QUEUE => new QueueConsumer($connection, $messageMapper),
            DestinationInterface::TYPE_FANOUT => new FanoutConsumer(
                $connection,
                $messageMapper,
                $this->getDestinationFactory()
            )
        ];
    }

    /**
     * @return \Jellyfish\QueueRabbitMq\ProducerInterface[]
     */
    protected function createProducers(): array
    {
        $connection = $this->getConnection();
        $messageMapper = new MessageMapper($this->serializerFacade);
        $amqpMessageFactory = new AmqpMessageFactory();

        return [
            DestinationInterface::TYPE_QUEUE => new QueueProducer($connection, $messageMapper, $amqpMessageFactory),
            DestinationInterface::TYPE_FANOUT => new FanoutProducer($connection, $messageMapper, $amqpMessageFactory),
        ];
    }

    /**
     * @return \Jellyfish\QueueRabbitMq\DestinationFactoryInterface
     */
    protected function getDestinationFactory(): DestinationFactoryInterface
    {
        if ($this->destinationFactory === null) {
            $this->destinationFactory = new DestinationFactory();
        }

        return $this->destinationFactory;
    }
}
