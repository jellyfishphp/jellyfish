<?php

namespace Jellyfish\Queue;

interface WorkerInterface
{
    /**
     * @return void
     */
    public function start(): void;
}
