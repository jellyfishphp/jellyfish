<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

interface ActivityManagerInterface
{
    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function start(int $activityId): ActivityManagerInterface;

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function stop(int $activityId): ActivityManagerInterface;

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function restart(int $activityId): ActivityManagerInterface;

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function stopAll(): ActivityManagerInterface;

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function startAll(): ActivityManagerInterface;

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function restartAll(): ActivityManagerInterface;
}
