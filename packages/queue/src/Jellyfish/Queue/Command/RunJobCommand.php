<?php

namespace Jellyfish\Queue\Command;

use Jellyfish\Queue\JobManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunJobCommand extends Command
{
    public const NAME = 'queue:job:run';
    public const DESCRIPTION = 'Run queue job';

    /**
     * @var \Jellyfish\Queue\JobManagerInterface
     */
    protected $jobManager;

    /**
     * RunJobCommand constructor.
     * @param \Jellyfish\Queue\JobManagerInterface $jobManager
     */
    public function __construct(JobManagerInterface $jobManager)
    {
        parent::__construct(null);

        $this->jobManager = $jobManager;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);
        $this->addArgument('queue', InputArgument::REQUIRED, 'Name of the job queue');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $queue = (string) $input->getArgument('queue');

        $this->jobManager->runJob($queue);

        return null;
    }
}
