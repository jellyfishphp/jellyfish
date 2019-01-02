<?php

namespace Jellyfish\SerializerSymfony;

use Codeception\Test\Unit;
use Pimple\Container;

class SerializerServiceProviderTest extends Unit
{
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

        $this->container = new Container();

        $this->serializerSymfonyServiceProvider = new SerializerSymfonyServiceProvider();
    }

    public function testRegister(): void
    {
        $this->serializerSymfonyServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists('serializer'));
        $this->assertInstanceOf(Serializer::class, $this->container->offsetGet('serializer'));
    }
}
