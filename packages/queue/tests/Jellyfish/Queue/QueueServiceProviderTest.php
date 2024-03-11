<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

use Codeception\Test\Unit;
use Jellyfish\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Pimple\Container;

class QueueServiceProviderTest extends Unit
{
    protected SerializerInterface&MockObject $serializerMock;

    protected Container $container;

    protected QueueServiceProvider $queueServiceProvider;

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

        $this->container->offsetSet('serializer', static fn(): MockObject => $self->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->queueServiceProvider = new QueueServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->queueServiceProvider->register($this->container);

        $messageMapper = $this->container->offsetGet(QueueConstants::CONTAINER_KEY_MESSAGE_MAPPER);
        $this->assertInstanceOf(MessageMapper::class, $messageMapper);

        $messageFactory = $this->container->offsetGet(QueueConstants::CONTAINER_KEY_MESSAGE_FACTORY);
        $this->assertInstanceOf(MessageFactory::class, $messageFactory);

        $destinationFactory = $this->container->offsetGet(QueueConstants::CONTAINER_KEY_DESTINATION_FACTORY);
        $this->assertInstanceOf(DestinationFactory::class, $destinationFactory);
    }
}
