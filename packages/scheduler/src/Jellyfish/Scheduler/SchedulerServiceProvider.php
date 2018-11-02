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
            $commands[] = $self->createRunSchedulerCommand($container);

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
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Scheduler\Command\RunSchedulerCommand
     */
    protected function createRunSchedulerCommand(Container $container): RunSchedulerCommand
    {
        $scheduler = $container->offsetGet('scheduler');

        return new RunSchedulerCommand($scheduler);
    }
}
