<?php

declare(strict_types = 1);

namespace Jellyfish\Http;

use Codeception\Test\Unit;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use League\Route\Router;
use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface;

class HttpServiceProviderTest extends Unit
{
    protected Container $container;

    protected HttpServiceProvider $routeServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->routeServiceProvider = new HttpServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->routeServiceProvider->register($this->container);

        $containerKeys = $this->container->keys();

        $this->assertSame(['request', 'router', 'emitter'], $containerKeys);

        $this->assertNotNull($this->container->offsetGet('request'));
        $this->assertInstanceOf(ServerRequestInterface::class, $this->container->offsetGet('request'));

        $this->assertNotNull($this->container->offsetGet('router'));
        $this->assertInstanceOf(Router::class, $this->container->offsetGet('router'));

        $this->assertNotNull($this->container->offsetGet('emitter'));
        $this->assertInstanceOf(EmitterInterface::class, $this->container->offsetGet('emitter'));
    }
}
