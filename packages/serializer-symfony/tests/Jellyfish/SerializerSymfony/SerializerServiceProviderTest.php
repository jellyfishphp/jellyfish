<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use Codeception\Test\Unit;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
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

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->serializerSymfonyServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(SerializerConstants::FACADE));
        static::assertInstanceOf(
            SerializerFacadeInterface::class,
            $this->container->offsetGet(SerializerConstants::FACADE)
        );
    }
}
