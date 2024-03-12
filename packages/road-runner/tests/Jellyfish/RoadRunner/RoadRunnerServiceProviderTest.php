<?php

namespace Jellyfish\RoadRunner;

use Codeception\Test\Unit;
use Pimple\Container;
use Spiral\RoadRunner\Http\PSR7Worker;

class RoadRunnerServiceProviderTest extends Unit
{
    protected Container $container;

    protected RoadRunnerServiceProvider $serviceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->serviceProvider = new RoadRunnerServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->serviceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists('road-runner-worker-factory'));

        $this->assertInstanceOf(
            RoadRunnerWorkerFactory::class,
            $this->container->offsetGet('road-runner-worker-factory')
        );
    }
}
