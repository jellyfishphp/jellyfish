<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Generated\Transfer\ActivityMonitor\Activity;
use Generated\Transfer\Pm2\Activity as Pm2Activity;

interface ActivityMapperInterface
{
    /**
     * @param \Generated\Transfer\Pm2\Activity $pm2Activity
     *
     * @return \Generated\Transfer\ActivityMonitor\Activity
     */
    public function mapPm2ActivityToActivity(Pm2Activity $pm2Activity): Activity;

    /**
     * @param \Generated\Transfer\Pm2\Activity[] $pm2Activities
     *
     * @return \Generated\Transfer\ActivityMonitor\Activity[]
     */
    public function mapPm2ActivitiesToActivities(array $pm2Activities): array;
}
