<?php

declare(strict_types=1);

namespace Jellyfish\UuidRamsey;

use Codeception\Test\Unit;
use Jellyfish\Uuid\UuidConstants;
use Pimple\Container;

class UuidRamseyServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container|null
     */
    protected ?Container $container = null;

    /**
     * @var \Jellyfish\UuidRamsey\UuidRamseyServiceProvider|null
     */
    protected ?UuidRamseyServiceProvider $uuidRamseyServiceProvider = null;

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

        static::assertTrue($this->container->offsetExists(UuidConstants::FACADE));
        static::assertInstanceOf(
            UuidRamseyFacade::class,
            $this->container->offsetGet(UuidConstants::FACADE)
        );
    }
}
