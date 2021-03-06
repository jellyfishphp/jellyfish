<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Console\ConsoleConstants;
use Jellyfish\Console\ConsoleFacadeInterface;
use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Event\Command\EventQueueWorkerStartCommand;
use Jellyfish\Lock\LockConstants;
use Jellyfish\Lock\LockFacadeInterface;
use Jellyfish\Log\LogConstants;
use Jellyfish\Log\LogFacadeInterface;
use Jellyfish\Process\ProcessConstants;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Queue\QueueConstants;
use Jellyfish\Queue\QueueFacadeInterface;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Jellyfish\Uuid\UuidConstants;
use Jellyfish\Uuid\UuidFacadeInterface;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;

class EventServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Console\ConsoleFacadeInterface
     */
    protected $consoleFacadeMock;

    /**
     * @var \Pimple\Container;
     */
    protected $container;

    /**
     * @var \Jellyfish\Event\EventServiceProvider
     */
    protected $eventServiceProvider;

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

        $this->container->offsetSet('root_dir', static function () {
            return DIRECTORY_SEPARATOR;
        });

        $self = $this;

        $this->container->offsetSet(ConsoleConstants::FACADE, static function () use ($self) {
            return $self->consoleFacadeMock;
        });

        $this->container->offsetSet(SerializerConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(SerializerFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(LockConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(LockFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(LogConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(LogFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(ProcessConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(ProcessFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(QueueConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(QueueFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(UuidConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(UuidFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->eventServiceProvider = new EventServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->consoleFacadeMock->expects(static::atLeastOnce())
            ->method('addCommand')
            ->withConsecutive(
                [
                    static::callback(static function (Command $command) {
                        return $command instanceof EventQueueConsumeCommand;
                    })
                ],
                [
                    static::callback(static function (Command $command) {
                        return $command instanceof EventQueueWorkerStartCommand;
                    })
                ]
            )->willReturn($this->consoleFacadeMock);

        $this->eventServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(EventConstants::FACADE));
        static::assertInstanceOf(
            EventFacadeInterface::class,
            $this->container->offsetGet(EventConstants::FACADE)
        );

        static::assertTrue($this->container->offsetExists(ConsoleConstants::FACADE));
        static::assertInstanceOf(
            ConsoleFacadeInterface::class,
            $this->container->offsetGet(ConsoleConstants::FACADE)
        );
    }
}
