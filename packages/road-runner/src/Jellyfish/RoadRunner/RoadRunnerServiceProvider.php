<?php

namespace Jellyfish\RoadRunner;

use Nyholm\Psr7\Factory\Psr17Factory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @see \Jellyfish\RoadRunner\RoadRunnerServiceProviderTest
 */
class RoadRunnerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerWorker($pimple);
    }

    protected function registerWorker(Container $pimple): RoadRunnerServiceProvider
    {
        $roadRunnerWorkerFactory = $this->getRoadRunnerWorkerFactory();

        $pimple->offsetSet(
            'road-runner-worker-factory',
            static fn(): RoadRunnerWorkerFactoryInterface => $roadRunnerWorkerFactory
        );

        return $this;
    }

    /**
     * @return \Jellyfish\RoadRunner\RoadRunnerWorkerFactoryInterface
     */
    protected function getRoadRunnerWorkerFactory(): RoadRunnerWorkerFactoryInterface
    {
        $psr17Factory = new Psr17Factory();

        return new RoadRunnerWorkerFactory($psr17Factory);
    }
}
