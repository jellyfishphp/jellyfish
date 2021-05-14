<?php

declare(strict_types=1);

namespace Jellyfish\Console;

use Codeception\Test\Unit;
use Pimple\Container;

class ConsoleServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected Container $container;

    /**
     * @var \Jellyfish\Console\ConsoleServiceProvider
     */
    protected ConsoleServiceProvider $consoleServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->consoleServiceProvider = new ConsoleServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->consoleServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(ConsoleConstants::FACADE));
        static::assertInstanceOf(
            ConsoleFacadeInterface::class,
            $this->container->offsetGet(ConsoleConstants::FACADE)
        );
    }
}
