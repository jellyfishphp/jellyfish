<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Process\ProcessConstants;
use Jellyfish\Process\ProcessFacadeInterface;
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

        $this->container->offsetSet(ProcessConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(ProcessFacadeInterface::class)
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
        static::assertCount(1, $commands);
        static::assertInstanceOf(RunSchedulerCommand::class, $commands[0]);

        $scheduler = $this->container->offsetGet(SchedulerConstants::FACADE);
        static::assertInstanceOf(SchedulerFacade::class, $scheduler);
    }
}
