<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Jellyfish\Event\EventFacadeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventQueueWorkerStartCommand extends Command
{
    public const NAME = 'event:queue-worker:start';
    public const DESCRIPTION = 'Start event queue worker';

    /**
     * @var \Jellyfish\Event\EventFacadeInterface
     */
    protected $eventFacade;

    /**
     * @param \Jellyfish\Event\EventFacadeInterface $eventFacade
     */
    public function __construct(
        EventFacadeInterface $eventFacade
    ) {
        parent::__construct();

        $this->eventFacade = $eventFacade;
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
        $this->eventFacade->startEventQueueWorker();

        return 0;
    }
}
