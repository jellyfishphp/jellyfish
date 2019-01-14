<?php

namespace Jellyfish\Scheduler\Command;

use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockTrait;
use Jellyfish\Scheduler\SchedulerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunSchedulerCommand extends Command
{
    use LockTrait;

    public const NAME = 'scheduler:run';
    public const DESCRIPTION = 'Run scheduler.';

    /**
     * @var \Jellyfish\Scheduler\SchedulerInterface
     */
    protected $scheduler;

    /**
     * @param \Jellyfish\Scheduler\SchedulerInterface $scheduler
     * @param \Jellyfish\Lock\LockFactoryInterface $lockFactory
     */
    public function __construct(
        SchedulerInterface $scheduler,
        LockFactoryInterface $lockFactory
    ) {
        parent::__construct();

        $this->scheduler = $scheduler;
        $this->lockFactory = $lockFactory;
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
        $lockIdentifier = $this->createIdentifier([static::NAME]);

        if (!$this->acquire($lockIdentifier)) {
            return null;
        }

        $this->scheduler->run();

        $this->release();

        return null;
    }
}
