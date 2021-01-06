<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Jellyfish\Process\ProcessConstants;
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
        $this->registerSchedulerFacade($pimple)
            ->registerCommands($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected function registerSchedulerFacade(Container $container): SchedulerServiceProvider
    {
        $container->offsetSet(SchedulerConstants::FACADE, static function (Container $container) {
            $schedulerFactory = new SchedulerFactory($container->offsetGet(ProcessConstants::FACADE));

            return new SchedulerFacade($schedulerFactory);
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
        $container->extend('commands', static function (array $commands, Container $container) {
            $commands[] = new RunSchedulerCommand(
                $container->offsetGet(SchedulerConstants::FACADE),
                $container->offsetGet('lock_factory'),
                $container->offsetGet('logger')
            );

            return $commands;
        });

        return $this;
    }
}
