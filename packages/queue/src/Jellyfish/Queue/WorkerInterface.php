<?php

namespace Jellyfish\Queue;

interface WorkerInterface
{
    /**
     * @return \Jellyfish\Queue\WorkerInterface
     */
    public function start(): WorkerInterface;
}
