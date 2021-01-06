<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Event\Command\EventQueueWorkerStartCommand;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Log\LogConstants;
use Jellyfish\Log\LogServiceProvider;
use Jellyfish\Process\ProcessConstants;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Queue\DestinationFactoryInterface;
use Jellyfish\Queue\MessageFactoryInterface;
use Jellyfish\Queue\QueueClientInterface;
use Jellyfish\Queue\QueueConstants;
use Jellyfish\Queue\QueueServiceProvider;
use Jellyfish\Serializer\SerializerInterface;
use Jellyfish\Uuid\UuidConstants;
use Jellyfish\Uuid\UuidFacadeInterface;
use Jellyfish\Uuid\UuidGeneratorInterface;
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

        $this->container->offsetSet('root_dir', static function () {
            return DIRECTORY_SEPARATOR;
        });

        $this->container->offsetSet('commands', static function () {
            return [];
        });

        $this->container->offsetSet('serializer', static function () use ($self) {
            return $self->getMockBuilder(SerializerInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('lock_factory', static function () use ($self) {
            return $self->getMockBuilder(LockFactoryInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(LogConstants::CONTAINER_KEY_LOGGER, static function () use ($self) {
            return $self->getMockBuilder(LoggerInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('message_factory', static function () use ($self) {
            return $self->getMockBuilder(MessageFactoryInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(ProcessConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(ProcessFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(QueueConstants::CONTAINER_KEY_QUEUE_CLIENT, static function () use ($self) {
            return $self->getMockBuilder(QueueClientInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(QueueConstants::CONTAINER_KEY_DESTINATION_FACTORY, static function () use ($self) {
            return $self->getMockBuilder(DestinationFactoryInterface::class)
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
        $this->eventServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(EventConstants::CONTAINER_KEY_EVENT_FACTORY));
        static::assertInstanceOf(EventFactory::class, $this->container->offsetGet(EventConstants::CONTAINER_KEY_EVENT_FACTORY));

        static::assertTrue($this->container->offsetExists(EventConstants::CONTAINER_KEY_EVENT_DISPATCHER));
        static::assertInstanceOf(EventDispatcher::class, $this->container->offsetGet(EventConstants::CONTAINER_KEY_EVENT_DISPATCHER));

        static::assertTrue($this->container->offsetExists(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));
        static::assertIsArray($this->container->offsetGet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));

        static::assertTrue($this->container->offsetExists('commands'));

        $commands = $this->container->offsetGet('commands');

        static::assertCount(2, $commands);
        static::assertInstanceOf(EventQueueConsumeCommand::class, $commands[0]);
        static::assertInstanceOf(EventQueueWorkerStartCommand::class, $commands[1]);
    }
}
