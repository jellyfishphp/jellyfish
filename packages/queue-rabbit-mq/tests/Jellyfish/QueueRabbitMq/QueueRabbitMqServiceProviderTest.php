<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Queue\QueueConstants;
use Jellyfish\Queue\QueueFacadeInterface;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Pimple\Container;

class QueueRabbitMqServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\QueueRabbitMq\QueueRabbitMqServiceProvider
     */
    protected $queueRabbitMqServiceProvider;

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

        $this->container->offsetSet(ConfigConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(ConfigFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(SerializerConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(SerializerFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->queueRabbitMqServiceProvider = new QueueRabbitMqServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->queueRabbitMqServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(QueueConstants::FACADE));
        static::assertInstanceOf(
            QueueFacadeInterface::class,
            $this->container->offsetGet(QueueConstants::FACADE)
        );
    }
}
