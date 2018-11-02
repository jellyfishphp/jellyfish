<?php

namespace Jellyfish\Scheduler\Command;

use Jellyfish\Scheduler\SchedulerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunSchedulerCommand extends Command
{
    protected const NAME = 'scheduler:run';
    protected const DESCRIPTION = 'Run scheduler.';

    /**
     * @var \Jellyfish\Scheduler\SchedulerInterface
     */
    protected $scheduler;

    /**
     * @param \Jellyfish\Scheduler\SchedulerInterface $scheduler
     */
    public function __construct(SchedulerInterface $scheduler)
    {
        parent::__construct(null);

        $this->scheduler = $scheduler;
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
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->scheduler->run();

        return null;
    }
}
