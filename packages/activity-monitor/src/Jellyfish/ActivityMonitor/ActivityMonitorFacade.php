<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

class ActivityMonitorFacade implements ActivityMonitorFacadeInterface
{
    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMonitorFactory
     */
    protected ActivityMonitorFactory $factory;

    /**
     * @param \Jellyfish\ActivityMonitor\ActivityMonitorFactory $factory
     */
    public function __construct(ActivityMonitorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return \Generated\Transfer\ActivityMonitor\Activity[]
     */
    public function getAllActivities(): array
    {
        return $this->factory->getActivityReader()->getAll();
    }

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertPropertyNameAfterNormalize(string $propertyName, ?string $class, ?string $format): ?string
    {
        return $this->factory->getPropertyNameConverter()->convertAfterNormalize($propertyName, $class, $format);
    }

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertPropertyNameAfterDenormalize(string $propertyName, ?string $class, ?string $format): ?string
    {
        return $this->factory->getPropertyNameConverter()->convertAfterDenormalize($propertyName, $class, $format);
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function startActivity(int $activityId): ActivityMonitorFacadeInterface
    {
        $this->factory->getActivityManager()->start($activityId);

        return $this;
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function restartActivity(int $activityId): ActivityMonitorFacadeInterface
    {
        $this->factory->getActivityManager()->restart($activityId);

        return $this;
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function stopActivity(int $activityId): ActivityMonitorFacadeInterface
    {
        $this->factory->getActivityManager()->stop($activityId);

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function startAllActivities(): ActivityMonitorFacadeInterface
    {
        $this->factory->getActivityManager()->startAll();

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function restartAllActivities(): ActivityMonitorFacadeInterface
    {
        $this->factory->getActivityManager()->restartAll();

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function stopAllActivities(): ActivityMonitorFacadeInterface
    {
        $this->factory->getActivityManager()->stopAll();

        return $this;
    }
}
