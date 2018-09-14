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
        while (true) {
            $queueNames = $this->jobManager->getQueueNames();

            foreach ($queueNames as $queueName) {
                //TODO: Run job in background
            }
        }
    }
}
