<?php

namespace Jellyfish\Queue;

class Worker implements WorkerInterface
{
    /**
     * @var \Jellyfish\Queue\JobManagerInterface
     */
    protected $jobManager;

    /**
     * @param \Jellyfish\Queue\JobManagerInterface $jobManager
     */
    public function __construct(
        JobManagerInterface $jobManager
    ) {
        $this->jobManager = $jobManager;
    }

    /**
     * @return void
     */
    public function start(): void
    {
        $queueNames = $this->jobManager->getQueueNames();

        while (true) {
            foreach ($queueNames as $queueName) {
                $this->jobManager->runJobAsProcess($queueName);
            }
        }
    }
}
