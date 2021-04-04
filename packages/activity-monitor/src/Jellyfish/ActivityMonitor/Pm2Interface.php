<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

interface Pm2Interface
{
    /**
     * @return \Generated\Transfer\Pm2\Activity[]
     */
    public function getActivities(): array;

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function startActivity(int $activityId): Pm2Interface;

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function restartActivity(int $activityId): Pm2Interface;

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function stopActivity(int $activityId): Pm2Interface;

    /**
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function restartAllActivities(): Pm2Interface;

    /**
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function stopAllActivities(): Pm2Interface;
}
