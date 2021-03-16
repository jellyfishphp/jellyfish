<?php

namespace Jellyfish\ActivityMonitor;

use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class ActivityMonitorFactory
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected $processFacade;
    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacade;

    /**
     * @param \Jellyfish\Process\ProcessFacadeInterface $processFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        ProcessFacadeInterface $processFacade,
        SerializerFacadeInterface $serializerFacade
    )
    {
        $this->processFacade = $processFacade;
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityReaderInterface
     */
    public function createActivityReader(): ActivityReaderInterface
    {
        return new ActivityReader($this->processFacade, $this->serializerFacade);
    }
}
