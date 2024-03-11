<?php

namespace Jellyfish\UuidRamsey;

use Codeception\Test\Unit;
use Jellyfish\Uuid\UuidConstants;
use Pimple\Container;

class UuidRamseyServiceProviderTest extends Unit
{
    protected Container $container;

    protected UuidRamseyServiceProvider $uuidRamseyServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();
        $this->uuidRamseyServiceProvider = new UuidRamseyServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->uuidRamseyServiceProvider->register($this->container);

        self::assertTrue($this->container->offsetExists(UuidConstants::CONTAINER_KEY_UUID_GENERATOR));
        self::assertInstanceOf(
            UuidGenerator::class,
            $this->container->offsetGet(UuidConstants::CONTAINER_KEY_UUID_GENERATOR)
        );
    }
}
