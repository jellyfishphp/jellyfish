<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler\Command;

use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockTrait;
use Jellyfish\Log\LogFacadeInterface;
use Jellyfish\Scheduler\SchedulerFacadeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class RunSchedulerCommand extends Command
{
    use LockTrait;

    public const NAME = 'scheduler:run';
    public const DESCRIPTION = 'Run scheduler.';

    /**
     * @var \Jellyfish\Scheduler\SchedulerFacadeInterface
     */
    protected $schedulerFacade;

    /**
     * @var \Jellyfish\Log\LogFacadeInterface
     */
    protected $logFacade;

    /**
     * @param \Jellyfish\Scheduler\SchedulerFacadeInterface $schedulerFacade
     * @param \Jellyfish\Lock\LockFactoryInterface $lockFactory
     * @param \Jellyfish\Log\LogFacadeInterface $logFacade
     */
    public function __construct(
        SchedulerFacadeInterface $schedulerFacade,
        LockFactoryInterface $lockFactory,
        LogFacadeInterface $logFacade
    ) {
        parent::__construct();

        $this->schedulerFacade = $schedulerFacade;
        $this->lockFactory = $lockFactory;
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
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $lockIdentifierParts = [static::NAME];

        if (!$this->acquire($lockIdentifierParts)) {
            return null;
        }

        try {
            $this->schedulerFacade->runScheduler();
        } catch (Throwable $e) {
            $this->logFacade->error($e->getMessage());
        } finally {
            $this->release();
        }

        return null;
    }
}
