<?php

declare(strict_types=1);

namespace Jellyfish\Telemetry;

use Jellyfish\Config\ConfigConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TelemetryServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerTelemetryFacade($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Telemetry\TelemetryServiceProvider
     */
    protected function registerTelemetryFacade(Container $container): TelemetryServiceProvider
    {
        $container->offsetSet(
            TelemetryConstants::FACADE,
            static fn (Container $container) => new TelemetryFacade(
                new TelemetryFactory($container->offsetGet(ConfigConstants::FACADE))
            )
        );

        return $this;
    }
}
