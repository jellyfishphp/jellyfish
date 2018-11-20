<?php

namespace Jellyfish\Queue;

interface JobManagerInterface
{
    /**
     * @param string $queueName
     * @return bool
     */
    public function existsJob(string $queueName): bool;

    /**
     * @param string $queueName
     * @return \Jellyfish\Queue\JobInterface|null
     */
    public function getJob(string $queueName): ?JobInterface;

    /**
     * @return \Jellyfish\Queue\JobInterface[]
     */
    public function getAllJobs(): array;

    /**
     * @return string[]
     */
    public function getQueueNames(): array;

    /**
     * @param string $queueName
     * @param \Jellyfish\Queue\JobInterface $job
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function setJob(string $queueName, JobInterface $job): JobManagerInterface;

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function unsetJob(string $queueName): JobManagerInterface;

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function runJob(string $queueName): JobManagerInterface;

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function runJobAsProcess(string $queueName): JobManagerInterface;
}
