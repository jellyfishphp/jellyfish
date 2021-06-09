<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Codeception\Test\Unit;
use Pimple\Container;

class RoadRunnerServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected Container $container;

    /**
     * @var \Jellyfish\RoadRunner\RoadRunnerServiceProvider
     */
    protected RoadRunnerServiceProvider $roadRunnerServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->roadRunnerServiceProvider = new RoadRunnerServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->roadRunnerServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(RoadRunnerConstants::FACADE));
        static::assertInstanceOf(
            RoadRunnerFacadeInterface::class,
            $this->container->offsetGet(RoadRunnerConstants::FACADE)
        );
    }
}
