<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Event\Command\EventQueueWorkerStartCommand;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Queue\MessageFactoryInterface;
use Jellyfish\Queue\QueueClientInterface;
use Jellyfish\Serializer\SerializerInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class EventServiceProviderTest extends Unit
{
    /**
     * @var \Jellyfish\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerMock;

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

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet('root_dir', function () {
            return DIRECTORY_SEPARATOR;
        });

        $this->container->offsetSet('commands', function () {
            return [];
        });

        $this->container->offsetSet('serializer', function () use ($self) {
            return $self->getMockBuilder(SerializerInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('lock_factory', function () use ($self) {
            return $self->getMockBuilder(LockFactoryInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('logger', function () use ($self) {
            return $self->getMockBuilder(LoggerInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('message_factory', function () use ($self) {
            return $self->getMockBuilder(MessageFactoryInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('process_factory', function () use ($self) {
            return $self->getMockBuilder(ProcessFactoryInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('queue_client', function () use ($self) {
            return $self->getMockBuilder(QueueClientInterface::class)
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
        $this->eventServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists(EventServiceProvider::CONTAINER_KEY_EVENT_FACTORY));
        $this->assertInstanceOf(EventFactory::class, $this->container->offsetGet(EventServiceProvider::CONTAINER_KEY_EVENT_FACTORY));

        $this->assertTrue($this->container->offsetExists(EventServiceProvider::CONTAINER_KEY_EVENT_DISPATCHER));
        $this->assertInstanceOf(EventDispatcher::class, $this->container->offsetGet(EventServiceProvider::CONTAINER_KEY_EVENT_DISPATCHER));

        $this->assertTrue($this->container->offsetExists(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));
        $this->assertIsArray($this->container->offsetGet(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));

        $this->assertTrue($this->container->offsetExists('commands'));

        $commands = $this->container->offsetGet('commands');

        $this->assertCount(2, $commands);
        $this->assertInstanceOf(EventQueueConsumeCommand::class, $commands[0]);
        $this->assertInstanceOf(EventQueueWorkerStartCommand::class, $commands[1]);
    }
}
