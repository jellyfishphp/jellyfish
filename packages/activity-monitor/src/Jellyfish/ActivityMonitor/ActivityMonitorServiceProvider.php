<?php

namespace Jellyfish\ActivityMonitor;

use Jellyfish\Process\ProcessConstants;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
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
        $this->registerActivityMonitorFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorServiceProvider
     */
    protected function registerActivityMonitorFacade(Container $container): ActivityMonitorServiceProvider
    {
        $container->offsetSet(ActivityMonitorConstants::FACADE, static function(Container $container) {
            $activityMonitorFactory = new ActivityMonitorFactory(
                $container->offsetGet(ProcessConstants::FACADE),
                $container->offsetGet(SerializerConstants::FACADE)
            );

            return new ActivityMonitorFacade($activityMonitorFactory);
        });

        return $this;
    }
}
