<?php

namespace Jellyfish\SerializerSymfony;

use Codeception\Test\Unit;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProvider;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderTest;
use Pimple\Container;

class SerializerServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    protected $propertyNameConverterStrategyProviderMock;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\SerializerSymfony\SerializerSymfonyServiceProvider
     */
    protected $serializerSymfonyServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->propertyNameConverterStrategyProviderMock = $this->getMockBuilder(PropertyNameConverterStrategyProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new Container();

        $self = $this;

        $this->container->offsetSet('serializer_property_name_converter_strategy_provider', function () use ($self) {
            return $self->propertyNameConverterStrategyProviderMock;
        });

        $this->serializerSymfonyServiceProvider = new SerializerSymfonyServiceProvider();
    }

    public function testRegister(): void
    {
        $this->serializerSymfonyServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists('serializer'));
        $this->assertInstanceOf(Serializer::class, $this->container->offsetGet('serializer'));
    }
}
