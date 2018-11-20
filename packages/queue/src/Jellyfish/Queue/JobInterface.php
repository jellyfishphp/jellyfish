<?php

namespace Jellyfish\Queue;

interface JobInterface
{
    /**
     * @param string $message
     *
     * @return \Jellyfish\Queue\JobInterface
     */
    public function run(string $message): JobInterface;
}
