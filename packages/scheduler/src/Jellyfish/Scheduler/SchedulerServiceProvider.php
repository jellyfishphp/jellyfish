<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Scheduler\Command\RunSchedulerCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SchedulerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param  \Pimple\Container  $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerScheduler($pimple)
            ->registerCommands($pimple)
            ->registerJobFactory($pimple);
    }

    /**
     * @param  \Pimple\Container  $container
     *
     * @return \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected function registerScheduler(Container $container): SchedulerServiceProvider
    {
        $container->offsetSet(SchedulerConstants::CONTAINER_KEY_SCHEDULER, function () {
            return new Scheduler();
        });

        return $this;
    }

    /**
     * @param  \Pimple\Container  $container
     *
     * @return \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected function registerCommands(Container $container): SchedulerServiceProvider
    {
        $container->extend('commands', static function (array $commands, Container $container) {
            $commands[] = new RunSchedulerCommand(
                $container->offsetGet(SchedulerConstants::CONTAINER_KEY_SCHEDULER),
                $container->offsetGet('lock_factory'),
                $container->offsetGet('logger')
            );

            return $commands;
        });

        return $this;
    }

    /**
     * @param  \Pimple\Container  $container
     *
     * @return \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected function registerJobFactory(Container $container): SchedulerServiceProvider
    {
        $self = $this;

        $container->offsetSet(SchedulerConstants::CONTAINER_KEY_JOB_FACTORY, static function (Container $container) use ($self) {
            $processFactory = $self->getProcessFactory($container);
            if ($processFactory === null) {
                return null;
            }
            return new JobFactory($processFactory, $self->createCronExpressionFactory());
        });

        return $this;
    }

    /**
     * @param  \Pimple\Container  $container
     *
     * @return \Jellyfish\Process\ProcessFactoryInterface|null
     */
    protected function getProcessFactory(Container $container): ?ProcessFactoryInterface
    {
        if ($container->offsetExists('process_factory') === false) {
            return null;
        }

        return $container->offsetGet('process_factory');
    }

    /**
     * @return \Jellyfish\Scheduler\CronExpressionFactoryInterface
     */
    protected function createCronExpressionFactory(): CronExpressionFactoryInterface
    {
        return new CronExpressionFactory();
    }
}
