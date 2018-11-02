<?php

namespace Jellyfish\Queue;

use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Queue\Command\RunJobCommand;
use Jellyfish\Queue\Command\StartWorkerCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple A container instance
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $self = $this;

        $pimple->offsetSet('job_manager', function ($container) use ($self) {
            return $self->createJobManager(
                $container->offsetGet('queue_client'),
                $container->offsetGet('process_factory')
            );
        });

        $pimple->offsetSet('worker', function ($container) use ($self) {
            return $self->createWorker($container->offsetGet('job_manager'));
        });

        $pimple->extend('commands', function ($commands, $container) use ($self) {
            $commands[] = $self->createRunJobCommand($container->offsetGet('job_manager'));
            $commands[] = $self->createStartWorkerCommand($container->offsetGet('worker'));

            return $commands;
        });
    }

    /**
     * @param \Jellyfish\Queue\ClientInterface $client
     * @param \Jellyfish\Process\ProcessFactoryInterface $processFactory
     *
     * @return \Jellyfish\Queue\JobManagerInterface
     */
    protected function createJobManager(
        ClientInterface $client,
        ProcessFactoryInterface $processFactory
    ): JobManagerInterface {
        return new JobManager($client, $processFactory);
    }

    /**
     * @param \Jellyfish\Queue\JobManagerInterface $jobManager
     *
     * @return \Jellyfish\Queue\WorkerInterface
     */
    protected function createWorker(JobManagerInterface $jobManager): WorkerInterface
    {
        return new Worker($jobManager);
    }

    /**
     * @param \Jellyfish\Queue\JobManagerInterface $jobManager
     *
     * @return \Jellyfish\Queue\Command\RunJobCommand
     */
    protected function createRunJobCommand(JobManagerInterface $jobManager): RunJobCommand
    {
        return new RunJobCommand($jobManager);
    }

    /**
     * @param \Jellyfish\Queue\WorkerInterface $worker
     *
     * @return \Jellyfish\Queue\Command\StartWorkerCommand
     */
    protected function createStartWorkerCommand(WorkerInterface $worker): StartWorkerCommand
    {
        return new StartWorkerCommand($worker);
    }
}
