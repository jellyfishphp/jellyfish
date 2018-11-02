<?php

namespace Jellyfish\Queue;

class Worker
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
     * @return \Jellyfish\Queue\WorkerInterface
     */
    public function start(): WorkerInterface
    {
        $queueNames = $this->jobManager->getQueueNames();

        while (true) {
            foreach ($queueNames as $queueName) {
                $this->jobManager->runJobAsProcess($queueName);
            }
        }
    }
}
