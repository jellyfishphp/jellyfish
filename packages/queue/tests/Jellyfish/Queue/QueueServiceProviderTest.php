<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

use Codeception\Test\Unit;
use Jellyfish\Serializer\SerializerInterface;
use Pimple\Container;

class QueueServiceProviderTest extends Unit
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

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet('serializer', static function () use ($self) {
            return $self->getMockBuilder(SerializerInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->queueServiceProvider = new QueueServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->queueServiceProvider->register($this->container);

        $messageMapper = $this->container->offsetGet(QueueServiceProvider::CONTAINER_KEY_MESSAGE_MAPPER);
        self::assertInstanceOf(MessageMapper::class, $messageMapper);

        $messageFactory = $this->container->offsetGet(QueueServiceProvider::CONTAINER_KEY_MESSAGE_FACTORY);
        self::assertInstanceOf(MessageFactory::class, $messageFactory);

        $destinationFactory = $this->container->offsetGet(QueueServiceProvider::CONTAINER_KEY_DESTINATION_FACTORY);
        self::assertInstanceOf(DestinationFactory::class, $destinationFactory);
    }
}
