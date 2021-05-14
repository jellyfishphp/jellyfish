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
    protected ProcessFacadeInterface $processFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected SerializerFacadeInterface $serializerFacade;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityReaderInterface|null
     */
    protected ?ActivityReaderInterface $activityReader = null;

    /**
     * @var \Jellyfish\ActivityMonitor\PropertyNameConverterInterface|null
     */
    protected ?PropertyNameConverterInterface $propertyNameConverter = null;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityManagerInterface|null
     */
    protected ?ActivityManagerInterface $activityManager = null;

    /**
     * @var \Jellyfish\ActivityMonitor\Pm2Interface|null
     */
    protected ?Pm2Interface $pm2 = null;

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
                $this->getPm2(),
                $this->createActivityMapper()
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
            $this->activityManager = new ActivityManager($this->getPm2());
        }

        return $this->activityManager;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    protected function getPm2(): Pm2Interface
    {
        if ($this->pm2 === null) {
            $this->pm2 = new Pm2(
                $this->processFacade,
                $this->serializerFacade
            );
        }

        return $this->pm2;
    }
}
