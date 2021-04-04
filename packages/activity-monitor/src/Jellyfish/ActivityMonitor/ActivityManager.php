<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

class ActivityManager implements ActivityManagerInterface
{
    /**
     * @var \Jellyfish\ActivityMonitor\Pm2Interface
     */
    protected $pm2;

    /**
     * @param \Jellyfish\ActivityMonitor\Pm2Interface $pm2
     */
    public function __construct(
        Pm2Interface $pm2
    ) {
        $this->pm2 = $pm2;
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function start(int $activityId): ActivityManagerInterface
    {
        $this->pm2->startActivity($activityId);

        return $this;
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function stop(int $activityId): ActivityManagerInterface
    {
        $this->pm2->stopActivity($activityId);

        return $this;
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function restart(int $activityId): ActivityManagerInterface
    {
        $this->pm2->restartActivity($activityId);

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function stopAll(): ActivityManagerInterface
    {
        $this->pm2->stopAllActivities();

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function restartAll(): ActivityManagerInterface
    {
        $this->pm2->restartAllActivities();

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function startAll(): ActivityManagerInterface
    {
        return $this->restartAll();
    }
}
