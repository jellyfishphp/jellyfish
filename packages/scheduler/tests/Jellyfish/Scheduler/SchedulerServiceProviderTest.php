<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Jellyfish\Console\ConsoleConstants;
use Jellyfish\Console\ConsoleFacadeInterface;
use Jellyfish\Lock\LockConstants;
use Jellyfish\Lock\LockFacadeInterface;
use Jellyfish\Log\LogConstants;
use Jellyfish\Log\LogFacadeInterface;
use Jellyfish\Process\ProcessConstants;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Scheduler\Command\SchedulerRunCommand;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;

class SchedulerServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Console\ConsoleFacadeInterface
     */
    protected $consoleFacadeMock;

    /**
     * @var \Pimple\Container;
     */
    protected Container $container;

    /**
     * @var \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected SchedulerServiceProvider $schedulerServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->consoleFacadeMock = $this->getMockBuilder(ConsoleFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new Container();

        $self = $this;

        $this->container->offsetSet(ConsoleConstants::FACADE, static fn () => $self->consoleFacadeMock);

        $this->container->offsetSet(LockConstants::FACADE, static fn () => $self->getMockBuilder(LockFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet(LogConstants::FACADE, static fn () => $self->getMockBuilder(LogFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet(ProcessConstants::FACADE, static fn () => $self->getMockBuilder(ProcessFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->schedulerServiceProvider = new SchedulerServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->consoleFacadeMock->expects(static::atLeastOnce())
            ->method('addCommand')
            ->with(
                static::callback(static fn (Command $command) => $command instanceof SchedulerRunCommand)
            )->willReturn($this->consoleFacadeMock);

        $this->schedulerServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(ConsoleConstants::FACADE));
        static::assertInstanceOf(
            ConsoleFacadeInterface::class,
            $this->container->offsetGet(ConsoleConstants::FACADE)
        );

        $scheduler = $this->container->offsetGet(SchedulerConstants::FACADE);
        static::assertInstanceOf(SchedulerFacade::class, $scheduler);
    }
}
