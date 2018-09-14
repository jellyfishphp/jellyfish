<?php

namespace Jellyfish\Queue;

class JobManager implements JobManagerInterface
{
    /**
     * @var \Jellyfish\Queue\JobInterface[]
     */
    protected $jobs;

    /**
     * @var \Jellyfish\Queue\ClientInterface
     */
    protected $client;

    /**
     * @param \Jellyfish\Queue\ClientInterface $client
     */
    public function __construct(
        ClientInterface $client
    ) {
        $this->client = $client;
        $this->jobs = [];
    }

    /**
     * @param string $queueName
     * @return bool
     */
    public function exists(string $queueName): bool
    {
        return \array_key_exists($queueName, $this->jobs);
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function run(string $queueName): JobManagerInterface
    {
        $job = $this->get($queueName);

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
     * @return \Jellyfish\Queue\JobInterface|null
     */
    public function get(string $queueName): ?JobInterface
    {
        if (!$this->exists($queueName)) {
            return null;
        }

        return $this->jobs[$queueName];
    }

    /**
     * @return \Jellyfish\Queue\JobInterface[]
     */
    public function getAll(): array
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
    public function set(string $queueName, JobInterface $job): JobManagerInterface
    {
        $this->jobs[$queueName] = $job;

        return $this;
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function unset(string $queueName): JobManagerInterface
    {
        if (!$this->exists($queueName)) {
            return $this;
        }

        unset($this->jobs[$queueName]);

        return $this;
    }
}
