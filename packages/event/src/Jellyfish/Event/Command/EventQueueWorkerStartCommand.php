<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Jellyfish\Event\EventQueueWorkerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventQueueWorkerStartCommand extends Command
{
    /**
     * @var string
     */
    public const NAME = 'event:queue-worker:start';

    /**
     * @var string
     */
    public const DESCRIPTION = 'Start event queue worker';

    protected EventQueueWorkerInterface $eventQueueWorker;

    /**
     * @param \Jellyfish\Event\EventQueueWorkerInterface $eventQueueWorker
     */
    public function __construct(
        EventQueueWorkerInterface $eventQueueWorker
    ) {
        parent::__construct();

        $this->eventQueueWorker = $eventQueueWorker;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->eventQueueWorker->start();

        return 0;
    }
}
