<?php

declare(strict_types = 1);

namespace Jellyfish\Event\Command;

use InvalidArgumentException;
use Jellyfish\Event\EventBulkListenerInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\Event\EventListenerInterface;
use Jellyfish\Event\EventListenerProviderInterface;
use Jellyfish\Event\EventQueueConsumerInterface;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @see \Jellyfish\Event\Command\EventQueueConsumeCommandTest
 */
class EventQueueConsumeCommand extends Command
{
    use LockTrait;

    /**
     * @var string
     */
    public const NAME = 'event:queue:consume';

    /**
     * @var string
     */
    public const DESCRIPTION = 'Consume from event queue';

    protected EventListenerProviderInterface $eventDispatcher;

    protected EventQueueConsumerInterface $eventQueueConsumer;

    protected LoggerInterface $logger;

    /**
     * @param \Jellyfish\Event\EventListenerProviderInterface $eventDispatcher
     * @param \Jellyfish\Event\EventQueueConsumerInterface $eventQueueConsumer
     * @param \Jellyfish\Lock\LockFactoryInterface $lockFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        EventListenerProviderInterface $eventDispatcher,
        EventQueueConsumerInterface $eventQueueConsumer,
        LockFactoryInterface $lockFactory,
        LoggerInterface $logger
    ) {
        parent::__construct();

        $this->eventDispatcher = $eventDispatcher;
        $this->eventQueueConsumer = $eventQueueConsumer;
        $this->lockFactory = $lockFactory;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);

        $this->addArgument('eventName', InputArgument::REQUIRED, 'Event name');
        $this->addArgument('listenerIdentifier', InputArgument::REQUIRED, 'Listener identifier');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $eventName = $input->getArgument('eventName');
        $listenerIdentifier = $input->getArgument('listenerIdentifier');

        if (!\is_string($eventName) || !\is_string($listenerIdentifier)) {
            throw new InvalidArgumentException('Unsupported type for given arguments');
        }

        $lockIdentifierParts = [static::NAME, $eventName, $listenerIdentifier];

        if (!$this->acquire($lockIdentifierParts)) {
            return 0;
        }

// TODO: Check before merge
        $result = null;

        try {
            $result = $this->executeLockablePart($eventName, $listenerIdentifier);
        } catch (Throwable $throwable) {
            $this->logger->error((string)$throwable);
        } finally {
            $this->release();
        }

        return 0;
    }

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return int|null
     */
    protected function executeLockablePart(string $eventName, string $listenerIdentifier): ?int
    {
        $listener = $this->eventDispatcher
            ->getListener(EventListenerInterface::TYPE_ASYNC, $eventName, $listenerIdentifier);

        if (!$listener instanceof EventListenerInterface) {
            return null;
        }

        if ($listener instanceof EventBulkListenerInterface) {
            $events = $this->eventQueueConsumer
                ->dequeueBulk($eventName, $listenerIdentifier, $listener->getChunkSize());

            $listener->handleBulk($events);

            return null;
        }

        $event = $this->eventQueueConsumer->dequeue($eventName, $listenerIdentifier);

        if (!$event instanceof EventInterface) {
            return null;
        }

        $listener->handle($event);

        return null;
    }
}
