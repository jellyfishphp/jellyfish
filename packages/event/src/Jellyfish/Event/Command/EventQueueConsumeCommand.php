<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use InvalidArgumentException;
use Jellyfish\Event\EventBulkListenerInterface;
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

use function is_string;

class EventQueueConsumeCommand extends Command
{
    use LockTrait;

    public const NAME = 'event:queue:consume';
    public const DESCRIPTION = 'Consume from event queue';

    /**
     * @param \Jellyfish\Event\EventListenerProviderInterface $eventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param \Jellyfish\Event\EventQueueConsumerInterface $eventQueueConsumer
     */
    protected $eventQueueConsumer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $eventName = $input->getArgument('eventName');
        $listenerIdentifier = $input->getArgument('listenerIdentifier');

        if (!is_string($eventName) || !is_string($listenerIdentifier)) {
            throw new InvalidArgumentException('Unsupported type for given arguments');
        }

        $lockIdentifierParts = [static::NAME, $eventName, $listenerIdentifier];

        if (!$this->acquire($lockIdentifierParts)) {
            return null;
        }

        $result = null;

        try {
            $result = $this->executeLockablePart($eventName, $listenerIdentifier);
        } catch (Throwable $e) {
            $this->logger->error((string)$e);
        } finally {
            $this->release();
        }

        return $result;
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

        if ($listener === null) {
            return null;
        }

        if ($listener instanceof EventBulkListenerInterface) {
            $events = $this->eventQueueConsumer
                ->dequeueBulk($eventName, $listenerIdentifier, $listener->getChunkSize());

            $listener->handleBulk($events);

            return null;
        }

        $event = $this->eventQueueConsumer->dequeue($eventName, $listenerIdentifier);

        if ($event === null) {
            return null;
        }

        $listener->handle($event);

        return null;
    }
}
