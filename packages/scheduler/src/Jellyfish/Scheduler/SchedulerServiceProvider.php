<?php

namespace Jellyfish\Scheduler;

use Jellyfish\Lock\LockFactoryInterface;
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

        $pimple->offsetSet('scheduler', function () use ($self) {
            return $self->createScheduler();
        });

        $pimple->extend('commands', function (array $commands, Container $container) use ($self) {
            $commands[] = $self->createRunSchedulerCommand(
                $container->offsetGet('scheduler'),
                $container->offsetGet('lock_factory')
            );

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
     * @param \Jellyfish\Lock\LockFactoryInterface $lockFactory
     *
     * @return \Jellyfish\Scheduler\Command\RunSchedulerCommand
     */
    protected function createRunSchedulerCommand(
        SchedulerInterface $scheduler,
        LockFactoryInterface $lockFactory
    ): RunSchedulerCommand {
        return new RunSchedulerCommand($scheduler, $lockFactory);
    }
}
