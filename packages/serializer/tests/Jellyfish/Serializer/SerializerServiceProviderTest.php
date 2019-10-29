<?php

declare(strict_types=1);

namespace Jellyfish\Serializer;

use Codeception\Test\Unit;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProvider;
use Pimple\Container;

class SerializerServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\Serializer\SerializerServiceProvider
     */
    protected $serializerServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->serializerServiceProvider = new SerializerServiceProvider();
    }

    public function testRegister(): void
    {
        $this->serializerServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists('serializer_property_name_converter_strategy_provider'));
        $this->assertInstanceOf(
            PropertyNameConverterStrategyProvider::class,
            $this->container->offsetGet('serializer_property_name_converter_strategy_provider')
        );
    }
}
