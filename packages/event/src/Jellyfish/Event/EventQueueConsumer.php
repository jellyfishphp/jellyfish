<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\QueueFacadeInterface;

use function array_key_exists;
use function sprintf;

class EventQueueConsumer implements EventQueueConsumerInterface
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected ProcessFacadeInterface $processFacade;

    /**
     * @var \Jellyfish\Event\EventMapperInterface
     */
    protected EventMapperInterface $eventMapper;

    /**
     * @var \Jellyfish\Event\EventQueueNameGeneratorInterface
     */
    protected EventQueueNameGeneratorInterface $eventQueueNameGenerator;

    /**
     * @var \Jellyfish\Queue\QueueFacadeInterface
     */
    protected QueueFacadeInterface $queueFacade;

    /**
     * @var \Jellyfish\Process\ProcessInterface[]
     */
    protected array $processList;

    /**
     * @var string
     */
    protected string $pathToConsole;

    /**
     * @param \Jellyfish\Process\ProcessFacadeInterface $processFacade
     * @param \Jellyfish\Event\EventMapperInterface $eventMapper
     * @param \Jellyfish\Event\EventQueueNameGeneratorInterface $eventQueueNameGenerator
     * @param \Jellyfish\Queue\QueueFacadeInterface $queueFacade
     * @param string $rootDir
     */
    public function __construct(
        ProcessFacadeInterface $processFacade,
        EventMapperInterface $eventMapper,
        EventQueueNameGeneratorInterface $eventQueueNameGenerator,
        QueueFacadeInterface $queueFacade,
        string $rootDir
    ) {
        $this->processFacade = $processFacade;
        $this->eventMapper = $eventMapper;
        $this->eventQueueNameGenerator = $eventQueueNameGenerator;
        $this->queueFacade = $queueFacade;
        $this->processList = [];
        $this->pathToConsole = sprintf('%svendor/bin/console', $rootDir);
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
            $this->processList[$eventQueueName] = $this->processFacade->createProcess($command);
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

        $destination = $this->queueFacade->createDestination()
            ->setName($eventQueueName)
            ->setType(DestinationInterface::TYPE_FANOUT)
            ->setProperty('bind', $eventName);

        $message = $this->queueFacade->receiveMessage($destination);

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

        $destination = $this->queueFacade->createDestination()
            ->setName($eventQueueName)
            ->setType(DestinationInterface::TYPE_FANOUT)
            ->setProperty('bind', $eventName);

        $messages = $this->queueFacade->receiveMessages($destination, $chunkSize);

        $events = [];

        foreach ($messages as $message) {
            $events[] = $this->eventMapper->fromMessage($message);
        }

        return $events;
    }
}
