<?php

namespace Jellyfish\Queue;

use Codeception\Test\Unit;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Queue\Command\RunJobCommand;
use Jellyfish\Queue\Command\StartWorkerCommand;
use Pimple\Container;

class QueueServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container;
     */
    protected $container;

    /**
     * @var \Jellyfish\Queue\QueueServiceProvider
     */
    protected $queueServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->container->offsetSet('queue_client', function ($container) {
            return $this->getMockForAbstractClass(ClientInterface::class);
        });

        $this->container->offsetSet('process_factory', function ($container) {
            return $this->getMockForAbstractClass(ProcessFactoryInterface::class);
        });

        $this->container->offsetSet('commands', function ($container) {
            return [];
        });

        $this->queueServiceProvider = new QueueServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->queueServiceProvider->register($this->container);

        $commands = $this->container->offsetGet('commands');
        $this->assertCount(2, $commands);
        $this->assertInstanceOf(RunJobCommand::class, $commands[0]);
        $this->assertInstanceOf(StartWorkerCommand::class, $commands[1]);

        $jobManager = $this->container->offsetGet('job_manager');
        $this->assertInstanceOf(JobManagerInterface::class, $jobManager);

        $worker = $this->container->offsetGet('worker');
        $this->assertInstanceOf(WorkerInterface::class, $worker);
    }
}
