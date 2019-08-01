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
        $this->registerScheduler($pimple)
            ->registerCommands($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected function registerScheduler(Container $container): SchedulerServiceProvider
    {
        $container->offsetSet('scheduler', function () {
            return new Scheduler();
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected function registerCommands(Container $container): SchedulerServiceProvider
    {
        $container->extend('commands', function (array $commands, Container $container) {
            $commands[] = new RunSchedulerCommand(
                $container->offsetGet('scheduler'),
                $container->offsetGet('lock_factory'),
                $container->offsetGet('logger')
            );

            return $commands;
        });

        return $this;
    }
}
