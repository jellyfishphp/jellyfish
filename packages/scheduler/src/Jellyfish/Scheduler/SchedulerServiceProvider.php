<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Jellyfish\Console\ConsoleConstants;
use Jellyfish\Console\ConsoleFacadeInterface;
use Jellyfish\Lock\LockConstants;
use Jellyfish\Log\LogConstants;
use Jellyfish\Process\ProcessConstants;
use Jellyfish\Scheduler\Command\RunSchedulerCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SchedulerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerSchedulerFacade($container)
            ->registerCommands($container);
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
        $container->extend(
            ConsoleConstants::FACADE,
            static function (ConsoleFacadeInterface $consoleFacade, Container $container) {
                $consoleFacade->addCommand(
                    new RunSchedulerCommand(
                        $container->offsetGet(SchedulerConstants::FACADE),
                        $container->offsetGet(LockConstants::FACADE),
                        $container->offsetGet(LogConstants::FACADE)
                    )
                );

                return $consoleFacade;
            }
        );

        return $this;
    }
}
