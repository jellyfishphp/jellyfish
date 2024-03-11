<?php

declare(strict_types = 1);

namespace Jellyfish\Scheduler;


interface JobFactoryInterface
{
    /**
     * @param  array  $command
     * @param  string  $cronExpression
     *
     * @return \Jellyfish\Scheduler\JobInterface
     */
    public function create(array $command, string $cronExpression): JobInterface;
}
