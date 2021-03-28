<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Jellyfish\ActivityMonitor\Serializer\NameConverter\PropertyNameConverterStrategy;
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
        $this->registerActivityMonitorFacade($container)
            ->registerPropertyNameConverterStrategy($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorServiceProvider
     */
    protected function registerActivityMonitorFacade(Container $container): ActivityMonitorServiceProvider
    {
        $container->offsetSet(ActivityMonitorConstants::FACADE, static function (Container $container) {
            $activityMonitorFactory = new ActivityMonitorFactory(
                $container->offsetGet(ProcessConstants::FACADE),
                $container->offsetGet(SerializerConstants::FACADE)
            );

            return new ActivityMonitorFacade($activityMonitorFactory);
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorServiceProvider
     */
    protected function registerPropertyNameConverterStrategy(Container $container): ActivityMonitorServiceProvider
    {
        $container->extend(
            SerializerConstants::FACADE,
            static function (SerializerFacadeInterface $serializerFacade, Container $container) {
                $propertyNameConverterStrategy = new PropertyNameConverterStrategy(
                    $container->offsetGet(ActivityMonitorConstants::FACADE)
                );

                return $serializerFacade->addPropertyNameConverterStrategy(
                    ActivityMonitorConstants::PROPERTY_NAME_CONVERTER_STRATEGY,
                    $propertyNameConverterStrategy
                );
            }
        );

        return $this;
    }
}
