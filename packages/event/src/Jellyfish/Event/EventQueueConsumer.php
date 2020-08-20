<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Queue\QueueClientInterface;

use function array_key_exists;

class EventQueueConsumer implements EventQueueConsumerInterface
{
    /**
     * @var \Jellyfish\Process\ProcessFactoryInterface
     */
    protected $processFactory;

    /**
     * @var \Jellyfish\Event\EventMapperInterface
     */
    protected $eventMapper;

    /**
     * @var \Jellyfish\Event\EventQueueNameGeneratorInterface
     */
    protected $eventQueueNameGenerator;

    /**
     * @var \Jellyfish\Queue\QueueClientInterface
     */
    protected $queueClient;

    /**
     * @var \Jellyfish\Process\ProcessInterface[]
     */
    protected $processList;

    /**
     * @var string
     */
    protected $pathToConsole;

    /**
     * @param \Jellyfish\Process\ProcessFactoryInterface $processFactory
     * @param \Jellyfish\Event\EventMapperInterface $eventMapper
     * @param \Jellyfish\Event\EventQueueNameGeneratorInterface $eventQueueNameGenerator
     * @param \Jellyfish\Queue\QueueClientInterface $queueClient
     * @param string $rootDir
     */
    public function __construct(
        ProcessFactoryInterface $processFactory,
        EventMapperInterface $eventMapper,
        EventQueueNameGeneratorInterface $eventQueueNameGenerator,
        QueueClientInterface $queueClient,
        string $rootDir
    ) {
        $this->processFactory = $processFactory;
        $this->eventMapper = $eventMapper;
        $this->eventQueueNameGenerator = $eventQueueNameGenerator;
        $this->queueClient = $queueClient;
        $this->processList = [];
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

        if (!array_key_exists($eventQueueName, $this->processList)) {
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

        $message = $this->queueClient->receiveMessage($eventQueueName);

        if ($message === null) {
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
        $messages = $this->queueClient->receiveMessages($eventQueueName, $chunkSize);
        $events = [];

        foreach ($messages as $message) {
            $events[] = $this->eventMapper->fromMessage($message);
        }

        return $events;
    }
}
