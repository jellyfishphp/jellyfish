<?php

namespace Jellyfish\ActivityMonitor;

use Jellyfish\Process\ProcessFacadeInterface;

class ActivityManager implements ActivityManagerInterface
{
    public const PM2_CLI = 'pm2';

    public const ARGUMENT_START = 'start';
    public const ARGUMENT_STOP = 'stop';
    public const ARGUMENT_RESTART = 'restart';
    public const ARGUMENT_ALL = 'all';

    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected $processFacade;

    /**
     * @param \Jellyfish\Process\ProcessFacadeInterface $processFacade
     */
    public function __construct(
        ProcessFacadeInterface $processFacade
    ) {
        $this->processFacade = $processFacade;
    }


    /**
     * @param int $activityId
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function start(int $activityId): ActivityManagerInterface
    {
        $process = $this->processFacade->createProcess([static::PM2_CLI, static::ARGUMENT_START, $activityId]);

        $process->start()
            ->wait();

        return $this;
    }

    /**
     * @param int $activityId
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function stop(int $activityId): ActivityManagerInterface
    {
        $process = $this->processFacade->createProcess([static::PM2_CLI, static::ARGUMENT_STOP, $activityId]);

        $process->start()
            ->wait();

        return $this;
    }

    /**
     * @param int $activityId
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function restart(int $activityId): ActivityManagerInterface
    {
        $process = $this->processFacade->createProcess([static::PM2_CLI, static::ARGUMENT_RESTART, $activityId]);

        $process->start()
            ->wait();

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function stopAll(): ActivityManagerInterface
    {
        $process = $this->processFacade->createProcess([static::PM2_CLI, static::ARGUMENT_STOP, static::ARGUMENT_ALL]);

        $process->start()
            ->wait();

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityManagerInterface
     */
    public function restartAll(): ActivityManagerInterface
    {
        $process = $this->processFacade->createProcess([static::PM2_CLI, static::ARGUMENT_RESTART, static::ARGUMENT_ALL]);

        $process->start()
            ->wait();

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
