<?php

declare(strict_types=1);

namespace Jellyfish\HttpLeague;

use Codeception\Test\Unit;
use Jellyfish\Http\HttpConstants;
use Jellyfish\Http\HttpFacadeInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HttpLeagueServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected Container $container;

    /**
     * @var \Pimple\ServiceProviderInterface
     */
    protected ServiceProviderInterface $routeServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->routeServiceProvider = new HttpLeagueServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->routeServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(HttpConstants::FACADE));
        static::assertInstanceOf(HttpFacadeInterface::class, $this->container->offsetGet(HttpConstants::FACADE));
    }
}
