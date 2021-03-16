<?php

namespace Jellyfish\ActivityMonitor;

interface ActivityMonitorFacadeInterface
{
    /**
     * @return array
     */
    public function getAllActivities(): array;
}
