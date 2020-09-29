<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Scheduler\Command\RunCommand;
use Jellyfish\Scheduler\Command\RunSchedulerCommand;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class SchedulerServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container;
     */
    protected $container;

    /**
     * @var \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected $schedulerServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet('commands', static function () {
            return [];
        });

        $this->container->offsetSet('lock_factory', static function () use ($self) {
            return $self->getMockBuilder(LockFactoryInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('logger', static function () use ($self) {
            return $self->getMockBuilder(LoggerInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->schedulerServiceProvider = new SchedulerServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $self = $this;
        
        $this->container->offsetSet('process_factory', static function () use ($self) {
            return $self->getMockBuilder(ProcessFactoryInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->schedulerServiceProvider->register($this->container);

        $commands = $this->container->offsetGet('commands');
        $this->assertCount(1, $commands);
        $this->assertInstanceOf(RunSchedulerCommand::class, $commands[0]);

        $scheduler = $this->container->offsetGet(SchedulerConstants::CONTAINER_KEY_SCHEDULER);
        $this->assertInstanceOf(Scheduler::class, $scheduler);

        $jobFactory = $this->container->offsetGet(SchedulerConstants::CONTAINER_KEY_JOB_FACTORY);
        $this->assertInstanceOf(JobFactory::class, $jobFactory);
    }

    /**
     * @return void
     */
    public function testRegisterProcessFactoryNotSet(): void
    {
        $this->schedulerServiceProvider->register($this->container);

        $jobFactory = $this->container->offsetGet(SchedulerConstants::CONTAINER_KEY_JOB_FACTORY);
        $this->assertNull($jobFactory);
    }
}
