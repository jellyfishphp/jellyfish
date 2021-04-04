<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

interface ActivityMonitorFacadeInterface
{
    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertPropertyNameAfterNormalize(
        string $propertyName,
        ?string $class,
        ?string $format
    ): ?string;

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string|null
     */
    public function convertPropertyNameAfterDenormalize(
        string $propertyName,
        ?string $class,
        ?string $format
    ): ?string;

    /**
     * @return \Generated\Transfer\ActivityMonitor\Activity[]
     */
    public function getAllActivities(): array;

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function startActivity(int $activityId): ActivityMonitorFacadeInterface;

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function restartActivity(int $activityId): ActivityMonitorFacadeInterface;

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function stopActivity(int $activityId): ActivityMonitorFacadeInterface;

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function startAllActivities(): ActivityMonitorFacadeInterface;

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function restartAllActivities(): ActivityMonitorFacadeInterface;

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface
     */
    public function stopAllActivities(): ActivityMonitorFacadeInterface;
}
