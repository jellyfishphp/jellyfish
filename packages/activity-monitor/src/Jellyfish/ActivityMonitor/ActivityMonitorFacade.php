<?php

namespace Jellyfish\ActivityMonitor;

class ActivityMonitorFacade implements ActivityMonitorFacadeInterface
{
    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMonitorFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\ActivityMonitor\ActivityMonitorFactory $factory
     */
    public function __construct(ActivityMonitorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function getAllActivities(): array
    {
        return $this->factory->createActivityReader()->getAll();
    }
}
