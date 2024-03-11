<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Event\Command\EventQueueWorkerStartCommand;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Log\LogConstants;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Queue\DestinationFactoryInterface;
use Jellyfish\Queue\MessageFactoryInterface;
use Jellyfish\Queue\QueueClientInterface;
use Jellyfish\Queue\QueueConstants;
use Jellyfish\Serializer\SerializerInterface;
use Jellyfish\Uuid\UuidConstants;
use Jellyfish\Uuid\UuidGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class EventServiceProviderTest extends Unit
{
    protected SerializerInterface&MockObject $serializerMock;

    protected Container $container;

    protected EventServiceProvider $eventServiceProvider;

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

        $this->container->offsetSet('root_dir', static fn (): string => DIRECTORY_SEPARATOR);

        $this->container->offsetSet('commands', static fn (): array => []);

        $this->container->offsetSet('serializer', static fn (): MockObject => $self->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet('lock_factory', static fn (): MockObject => $self->getMockBuilder(LockFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet(LogConstants::CONTAINER_KEY_LOGGER, static fn (): MockObject => $self->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet('message_factory', static fn (): MockObject => $self->getMockBuilder(MessageFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet('process_factory', static fn (): MockObject => $self->getMockBuilder(ProcessFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet(QueueConstants::CONTAINER_KEY_QUEUE_CLIENT, static fn (): MockObject => $self->getMockBuilder(QueueClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet(QueueConstants::CONTAINER_KEY_DESTINATION_FACTORY, static fn (): MockObject => $self->getMockBuilder(DestinationFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet(UuidConstants::CONTAINER_KEY_UUID_GENERATOR, static fn (): MockObject => $self->getMockBuilder(UuidGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->eventServiceProvider = new EventServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->eventServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists(EventConstants::CONTAINER_KEY_EVENT_FACTORY));
        $this->assertInstanceOf(EventFactory::class, $this->container->offsetGet(EventConstants::CONTAINER_KEY_EVENT_FACTORY));

        $this->assertTrue($this->container->offsetExists(EventConstants::CONTAINER_KEY_EVENT_DISPATCHER));
        $this->assertInstanceOf(EventDispatcher::class, $this->container->offsetGet(EventConstants::CONTAINER_KEY_EVENT_DISPATCHER));

        $this->assertTrue($this->container->offsetExists(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));
        $this->assertIsArray($this->container->offsetGet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));

        $this->assertTrue($this->container->offsetExists('commands'));

        $commands = $this->container->offsetGet('commands');

        $this->assertCount(2, $commands);
        $this->assertInstanceOf(EventQueueConsumeCommand::class, $commands[0]);
        $this->assertInstanceOf(EventQueueWorkerStartCommand::class, $commands[1]);
    }
}
