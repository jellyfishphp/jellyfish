<?php

namespace Jellyfish\Queue;

use Codeception\Test\Unit;
use Pimple\Container;
use Jellyfish\Serializer\SerializerInterface;

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

        $this->container->offsetSet('serializer', function () use ($self) {
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

        $messageMapper = $this->container->offsetGet('message_mapper');
        $this->assertInstanceOf(MessageMapper::class, $messageMapper);

        $messageFactory = $this->container->offsetGet('message_factory');
        $this->assertInstanceOf(MessageFactory::class, $messageFactory);
    }
}
