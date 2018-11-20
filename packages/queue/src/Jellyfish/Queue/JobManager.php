<?php

namespace Jellyfish\Queue;

use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Queue\Command\RunJobCommand;

class JobManager implements JobManagerInterface
{
    /**
     * @var \Jellyfish\Queue\JobInterface[]
     */
    protected $jobs;

    /**
     * @var \Jellyfish\Process\ProcessInterface[]
     */
    protected $processList;

    /**
     * @var \Jellyfish\Queue\ClientInterface
     */
    protected $client;

    /**
     * @var \Jellyfish\Process\ProcessFactoryInterface
     */
    protected $processFactory;

    /**
     * @param \Jellyfish\Queue\ClientInterface $client
     * @param \Jellyfish\Process\ProcessFactoryInterface $processFactory
     */
    public function __construct(
        ClientInterface $client,
        ProcessFactoryInterface $processFactory
    ) {
        $this->client = $client;
        $this->processFactory = $processFactory;
        $this->processList = [];
        $this->jobs = [];
    }

    /**
     * @param string $queueName
     * @return bool
     */
    public function existsJob(string $queueName): bool
    {
        return \array_key_exists($queueName, $this->jobs);
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function runJob(string $queueName): JobManagerInterface
    {
        $job = $this->getJob($queueName);

        if ($job === null) {
            return $this;
        }

        $message = $this->client->receiveMessage($queueName);
        $job->run($message);

        return $this;
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function runJobAsProcess(string $queueName): JobManagerInterface
    {
        if (!\array_key_exists($queueName, $this->processList)) {
            $command = ['', RunJobCommand::NAME, $queueName];
            $this->processList[$queueName] = $this->processFactory->create($command);
        }

        $process = $this->processList[$queueName];

        if (!$process->isLocked()) {
            $process->start();
        }

        return $this;
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobInterface|null
     */
    public function getJob(string $queueName): ?JobInterface
    {
        if (!$this->existsJob($queueName)) {
            return null;
        }

        return $this->jobs[$queueName];
    }

    /**
     * @return \Jellyfish\Queue\JobInterface[]
     */
    public function getAllJobs(): array
    {
        return $this->jobs;
    }

    /**
     * @return string[]
     */
    public function getQueueNames(): array
    {
        return \array_keys($this->jobs);
    }

    /**
     * @param string $queueName
     * @param \Jellyfish\Queue\JobInterface $job
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function setJob(string $queueName, JobInterface $job): JobManagerInterface
    {
        $this->jobs[$queueName] = $job;

        return $this;
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function unsetJob(string $queueName): JobManagerInterface
    {
        if (!$this->existsJob($queueName)) {
            return $this;
        }

        unset($this->jobs[$queueName]);

        return $this;
    }
}
