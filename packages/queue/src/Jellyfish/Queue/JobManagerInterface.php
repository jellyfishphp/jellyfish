<?php

namespace Jellyfish\Queue;

interface JobManagerInterface
{
    /**
     * @param string $queueName
     * @return bool
     */
    public function exists(string $queueName): bool;

    /**
     * @param string $queueName
     * @return \Jellyfish\Queue\JobInterface|null
     */
    public function get(string $queueName): ?JobInterface;

    /**
     * @return \Jellyfish\Queue\JobInterface[]
     */
    public function getAll(): array;

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
    public function set(string $queueName, JobInterface $job): JobManagerInterface;

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function unset(string $queueName): JobManagerInterface;

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    public function run(string $queueName): JobManagerInterface;
}
