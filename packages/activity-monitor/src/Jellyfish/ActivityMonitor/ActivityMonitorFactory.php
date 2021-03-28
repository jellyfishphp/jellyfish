<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class ActivityMonitorFactory
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected $processFacade;
    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacade;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityReaderInterface
     */
    protected $activityReader;

    /**
     * @var \Jellyfish\ActivityMonitor\PropertyNameConverterInterface
     */
    protected $propertyNameConverter;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    protected $activityManager;

    /**
     * @param \Jellyfish\Process\ProcessFacadeInterface $processFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        ProcessFacadeInterface $processFacade,
        SerializerFacadeInterface $serializerFacade
    ) {
        $this->processFacade = $processFacade;
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityReaderInterface
     */
    public function getActivityReader(): ActivityReaderInterface
    {
        if ($this->activityReader === null) {
            $this->activityReader = new ActivityReader(
                $this->createActivityMapper(),
                $this->processFacade,
                $this->serializerFacade
            );
        }

        return $this->activityReader;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityMapperInterface
     */
    protected function createActivityMapper(): ActivityMapperInterface
    {
        return new ActivityMapper();
    }

    /**
     * @return \Jellyfish\ActivityMonitor\PropertyNameConverterInterface
     */
    public function getPropertyNameConverter(): PropertyNameConverterInterface
    {
        if ($this->propertyNameConverter === null) {
            $this->propertyNameConverter = new PropertyNameConverter();
        }

        return $this->propertyNameConverter;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function getActivityManager(): ActivityManagerInterface
    {
        if ($this->activityManager === null) {
            $this->activityManager = new ActivityManager(
                $this->processFacade
            );
        }

        return $this->activityManager;
    }
}
