<?php

namespace Jellyfish\Queue\Command;

use Jellyfish\Queue\WorkerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartWorkerCommand extends Command
{
    protected const NAME = 'queue:worker:start';
    protected const DESCRIPTION = 'Start queue worker.';

    protected $worker;

    public function __construct(WorkerInterface $worker)
    {
        parent::__construct(null);

        $this->worker = $worker;
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
        $this->worker->start();

        return null;
    }
}
