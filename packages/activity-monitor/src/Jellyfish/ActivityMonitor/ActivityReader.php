<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Generated\Transfer\ActivityMonitor\Activity;

class ActivityReader implements ActivityReaderInterface
{
    /**
     * @var \Jellyfish\ActivityMonitor\Pm2Interface
     */
    protected Pm2Interface $pm2;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMapperInterface
     */
    protected ActivityMapperInterface $activityMapper;

    /**
     * @param \Jellyfish\ActivityMonitor\Pm2Interface $pm2
     * @param \Jellyfish\ActivityMonitor\ActivityMapperInterface $activityMapper
     */
    public function __construct(
        Pm2Interface $pm2,
        ActivityMapperInterface $activityMapper
    ) {
        $this->pm2 = $pm2;
        $this->activityMapper = $activityMapper;
    }

    /**
     * @return \Generated\Transfer\ActivityMonitor\Activity[]
     */
    public function getAll(): array
    {
        $pm2Activities = $this->pm2->getActivities();

        return $this->activityMapper->mapPm2ActivitiesToActivities($pm2Activities);
    }

    /**
     * @param int $id
     *
     * @return \Generated\Transfer\ActivityMonitor\Activity|null
     */
    public function getById(int $id): ?Activity
    {
        $activities = $this->getAll();

        foreach ($activities as $activity) {
            if ($activity->getId() === $id) {
                return $activity;
            }
        }

        return null;
    }
}
