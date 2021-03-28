<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Generated\Transfer\ActivityMonitor\Activity;
use Generated\Transfer\Pm2\Activity as Pm2Activity;

class ActivityMapper implements ActivityMapperInterface
{
    /**
     * @param \Generated\Transfer\Pm2\Activity $pm2Activity
     *
     * @return \Generated\Transfer\ActivityMonitor\Activity
     */
    public function mapPm2ActivityToActivity(Pm2Activity $pm2Activity): Activity
    {
        return (new Activity())->setId($pm2Activity->getPmId())
            ->setProcessId($pm2Activity->getPid())
            ->setName($pm2Activity->getName())
            ->setStatus($pm2Activity->getPm2Env()->getStatus());
    }

    /**
     * @param \Generated\Transfer\Pm2\Activity[] $pm2Activities
     *
     * @return \Generated\Transfer\ActivityMonitor\Activity[]
     */
    public function mapPm2ActivitiesToActivities(array $pm2Activities): array
    {
        $activities = [];

        foreach ($pm2Activities as $pm2Activity) {
            $activities[] = $this->mapPm2ActivityToActivity($pm2Activity);
        }

        return $activities;
    }
}
