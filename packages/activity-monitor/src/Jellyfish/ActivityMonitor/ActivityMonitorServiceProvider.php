<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Jellyfish\ActivityMonitor\Serializer\NameConverter\PropertyNameConverterStrategy;
use Jellyfish\Process\ProcessConstants;
use Jellyfish\Serializer\SerializerConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ActivityMonitorServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerActivityMonitorFacadeAndPropertyNameConverterStrategy($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorServiceProvider
     */
    protected function registerActivityMonitorFacadeAndPropertyNameConverterStrategy(
        Container $container
    ): ActivityMonitorServiceProvider {
        $container->offsetSet(ActivityMonitorConstants::FACADE, static function (Container $container) {
            $activityMonitorFactory = new ActivityMonitorFactory(
                $container->offsetGet(ProcessConstants::FACADE),
                $container->offsetGet(SerializerConstants::FACADE)
            );

            $activityMonitorFacade = new ActivityMonitorFacade($activityMonitorFactory);

            $container->offsetGet(SerializerConstants::FACADE)->addPropertyNameConverterStrategy(
                ActivityMonitorConstants::PROPERTY_NAME_CONVERTER_STRATEGY,
                new PropertyNameConverterStrategy($activityMonitorFacade)
            );

            return $activityMonitorFacade;
        });

        return $this;
    }
}
