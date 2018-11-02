<?php

namespace Jellyfish\Scheduler;

use Jellyfish\Scheduler\Command\RunSchedulerCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SchedulerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $self = $this;

        $pimple->offsetSet('scheduler', function ($container) use ($self) {
            return $self->createScheduler();
        });

        $pimple->extend('commands', function ($commands, $container) use ($self) {
            $commands[] = $self->createRunSchedulerCommand($container->offsetGet('scheduler'));

            return $commands;
        });
    }

    /**
     * @return \Jellyfish\Scheduler\SchedulerInterface
     */
    protected function createScheduler(): SchedulerInterface
    {
        return new Scheduler();
    }

    /**
     * @param \Jellyfish\Scheduler\SchedulerInterface $scheduler
     *
     * @return \Jellyfish\Scheduler\Command\RunSchedulerCommand
     */
    protected function createRunSchedulerCommand(SchedulerInterface $scheduler): RunSchedulerCommand
    {
        return new RunSchedulerCommand($scheduler);
    }
}
