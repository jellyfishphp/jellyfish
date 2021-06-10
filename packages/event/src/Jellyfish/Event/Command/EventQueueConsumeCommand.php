<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use InvalidArgumentException;
use Jellyfish\Event\EventBulkListenerInterface;
use Jellyfish\Event\EventFacadeInterface;
use Jellyfish\Event\EventListenerInterface;
use Jellyfish\Lock\LockFacadeInterface;
use Jellyfish\Lock\LockTrait;
use Jellyfish\Log\LogFacadeInterface;
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
     * @var \Jellyfish\Event\EventFacadeInterface
     */
    protected EventFacadeInterface $eventFacade;

    /**
     * @var \Jellyfish\Log\LogFacadeInterface
     */
    protected LogFacadeInterface $logFacade;

    /**
     * @param \Jellyfish\Event\EventFacadeInterface $eventFacade
     * @param \Jellyfish\Lock\LockFacadeInterface $lockFacade
     * @param \Jellyfish\Log\LogFacadeInterface $logFacade
     */
    public function __construct(
        EventFacadeInterface $eventFacade,
        LockFacadeInterface $lockFacade,
        LogFacadeInterface $logFacade
    ) {
        parent::__construct();

        $this->eventFacade = $eventFacade;
        $this->lockFacade = $lockFacade;
        $this->logFacade = $logFacade;
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

        if (!is_string($eventName) || !is_string($listenerIdentifier)) {
            throw new InvalidArgumentException('Unsupported type for given arguments');
        }

        $lockIdentifierParts = [static::NAME, $eventName, $listenerIdentifier];

        if (!$this->acquire($lockIdentifierParts)) {
            return 0;
        }

        $result = 0;

        try {
            $result = $this->executeLockablePart($eventName, $listenerIdentifier);
        } catch (Throwable $e) {
            $this->logFacade->getLogger()->error((string)$e);
        } finally {
            $this->release();
        }

        return $result;
    }

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return int
     */
    protected function executeLockablePart(string $eventName, string $listenerIdentifier): int
    {
        $listener = $this->eventFacade
            ->getEventListener(EventListenerInterface::TYPE_ASYNC, $eventName, $listenerIdentifier);

        if ($listener === null) {
            return 0;
        }

        if ($listener instanceof EventBulkListenerInterface) {
            $events = $this->eventFacade
                ->dequeueEventBulk($eventName, $listenerIdentifier, $listener->getChunkSize());

            $listener->handleBulk($events);

            return 0;
        }

        $event = $this->eventFacade->dequeueEvent($eventName, $listenerIdentifier);

        if ($event === null) {
            return 0;
        }

        $listener->handle($event);

        return 0;
    }
}
