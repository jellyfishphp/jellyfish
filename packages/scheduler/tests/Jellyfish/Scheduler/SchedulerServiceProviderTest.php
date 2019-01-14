<?php

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Scheduler\Command\RunCommand;
use Jellyfish\Scheduler\Command\RunSchedulerCommand;
use Pimple\Container;

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

        $this->container->offsetSet('commands', function () {
            return [];
        });

        $this->container->offsetSet('lock_factory', function () use ($self) {
            return $self->getMockBuilder(LockFactoryInterface::class)
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
        $this->schedulerServiceProvider->register($this->container);

        $commands = $this->container->offsetGet('commands');
        $this->assertCount(1, $commands);
        $this->assertInstanceOf(RunSchedulerCommand::class, $commands[0]);

        $scheduler = $this->container->offsetGet('scheduler');
        $this->assertInstanceOf(SchedulerInterface::class, $scheduler);
    }
}
