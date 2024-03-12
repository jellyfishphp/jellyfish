<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Queue\DestinationFactoryInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueClientInterface;

/**
 * @see \Jellyfish\Event\EventQueueConsumerTest
 */
class EventQueueConsumer implements EventQueueConsumerInterface
{
    protected ProcessFactoryInterface $processFactory;

    protected EventMapperInterface $eventMapper;

    protected EventQueueNameGeneratorInterface $eventQueueNameGenerator;

    protected QueueClientInterface $queueClient;

    /**
     * @var array<\Jellyfish\Process\ProcessInterface
     */
    protected array $processList = [];

    protected string $pathToConsole;

    protected DestinationFactoryInterface $destinationFactory;

    /**
     * @param \Jellyfish\Process\ProcessFactoryInterface $processFactory
     * @param \Jellyfish\Event\EventMapperInterface $eventMapper
     * @param \Jellyfish\Event\EventQueueNameGeneratorInterface $eventQueueNameGenerator
     * @param \Jellyfish\Queue\QueueClientInterface $queueClient
     * @param \Jellyfish\Queue\DestinationFactoryInterface $destinationFactory
     * @param string $rootDir
     */
    public function __construct(
        ProcessFactoryInterface $processFactory,
        EventMapperInterface $eventMapper,
        EventQueueNameGeneratorInterface $eventQueueNameGenerator,
        QueueClientInterface $queueClient,
        DestinationFactoryInterface $destinationFactory,
        string $rootDir
    ) {
        $this->processFactory = $processFactory;
        $this->eventMapper = $eventMapper;
        $this->eventQueueNameGenerator = $eventQueueNameGenerator;
        $this->queueClient = $queueClient;
        $this->destinationFactory = $destinationFactory;
        $this->pathToConsole = \sprintf('%svendor/bin/console', $rootDir);
    }

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventQueueConsumerInterface
     */
    public function dequeueAsProcess(string $eventName, string $listenerIdentifier): EventQueueConsumerInterface
    {
        $eventQueueName = $this->eventQueueNameGenerator->generate($eventName, $listenerIdentifier);

        if (!\array_key_exists($eventQueueName, $this->processList)) {
            $command = [$this->pathToConsole, EventQueueConsumeCommand::NAME, $eventName, $listenerIdentifier];
            $this->processList[$eventQueueName] = $this->processFactory->create($command);
        }

        $process = $this->processList[$eventQueueName];
        $process->start();

        return $this;
    }

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventInterface|null
     */
    public function dequeue(string $eventName, string $listenerIdentifier): ?EventInterface
    {
        $eventQueueName = $this->eventQueueNameGenerator->generate($eventName, $listenerIdentifier);

        $destination = $this->destinationFactory->create()
            ->setName($eventQueueName)
            ->setType(DestinationInterface::TYPE_FANOUT)
            ->setProperty('bind', $eventName);

        $message = $this->queueClient->receiveMessage($destination);

        if (!$message instanceof MessageInterface) {
            return null;
        }

        return $this->eventMapper->fromMessage($message);
    }

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     * @param int $chunkSize
     *
     * @return \Jellyfish\Event\EventInterface[]
     */
    public function dequeueBulk(string $eventName, string $listenerIdentifier, int $chunkSize): array
    {
        $eventQueueName = $this->eventQueueNameGenerator->generate($eventName, $listenerIdentifier);

        $destination = $this->destinationFactory->create()
            ->setName($eventQueueName)
            ->setType(DestinationInterface::TYPE_FANOUT)
            ->setProperty('bind', $eventName);

        $messages = $this->queueClient->receiveMessages($destination, $chunkSize);

        $events = [];

        foreach ($messages as $message) {
            $events[] = $this->eventMapper->fromMessage($message);
        }

        return $events;
    }
}
