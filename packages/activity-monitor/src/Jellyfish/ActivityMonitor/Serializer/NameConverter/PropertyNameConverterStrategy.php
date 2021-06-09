<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor\Serializer\NameConverter;

use Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface;

class PropertyNameConverterStrategy implements PropertyNameConverterStrategyInterface
{
    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    protected ActivityMonitorFacadeInterface $activityMonitorFacade;

    /**
     * @param \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface $activityMonitorFacade
     */
    public function __construct(ActivityMonitorFacadeInterface $activityMonitorFacade)
    {
        $this->activityMonitorFacade = $activityMonitorFacade;
    }

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertAfterNormalize(string $propertyName, ?string $class, ?string $format): ?string
    {
        return $this->activityMonitorFacade->convertPropertyNameAfterNormalize($propertyName, $class, $format);
    }

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertAfterDenormalize(string $propertyName, ?string $class, ?string $format): ?string
    {
        return $this->activityMonitorFacade->convertPropertyNameAfterDenormalize($propertyName, $class, $format);
    }
}
