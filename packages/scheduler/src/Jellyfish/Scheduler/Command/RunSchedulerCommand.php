<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler\Command;

use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockTrait;
use Jellyfish\Scheduler\SchedulerInterface;
use Psr\Log\LoggerInterface;
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
     * @var \Jellyfish\Scheduler\SchedulerInterface
     */
    protected $scheduler;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Jellyfish\Scheduler\SchedulerInterface $scheduler
     * @param \Jellyfish\Lock\LockFactoryInterface $lockFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        SchedulerInterface $scheduler,
        LockFactoryInterface $lockFactory,
        LoggerInterface $logger
    ) {
        parent::__construct();

        $this->scheduler = $scheduler;
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
            $this->scheduler->run();
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        } finally {
            $this->release();
        }

        return null;
    }
}
