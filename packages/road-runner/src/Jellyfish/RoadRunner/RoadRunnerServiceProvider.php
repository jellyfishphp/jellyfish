<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RoadRunnerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerRoadRunnerFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\RoadRunner\RoadRunnerServiceProvider
     */
    protected function registerRoadRunnerFacade(Container $container): RoadRunnerServiceProvider
    {
        $container->offsetSet(
            RoadRunnerConstants::FACADE,
            static fn () => new RoadRunnerFacade(new RoadRunnerFactory())
        );

        return $this;
    }
}
