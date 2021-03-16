<?php

namespace Jellyfish\ActivityMonitor;

use ArrayObject;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

use function sprintf;

class ActivityReader implements ActivityReaderInterface
{
    public const PM2_CLI = 'pm2';
    public const ARGUMENT_JLIST = 'jlist';

    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected $processFacade;
    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacade;

    /**
     * ActivityReader constructor.
     * @param \Jellyfish\Process\ProcessFacadeInterface $processFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        ProcessFacadeInterface $processFacade,
        SerializerFacadeInterface $serializerFacade
    ) {
        $this->processFacade = $processFacade;
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityInterface[]
     *
     * @throws \Jellyfish\Process\Exception\AlreadyStartedException
     * @throws \Jellyfish\Process\Exception\NotStartedException
     * @throws \Jellyfish\Process\Exception\NotTerminatedException
     */
    public function getAll(): array
    {
        $process = $this->processFacade->createProcess([static::PM2_CLI, static::ARGUMENT_JLIST]);

        $process->start()
            ->wait();

        if ($process->getExitCode() !== 0) {
            return [];
        }

        $activityList = $this->serializerFacade->deserialize(
            $process->getOutput(),
            sprintf('%s[]',Activity::class),
            'json'
        );

        if ($activityList instanceof ArrayObject) {
            return $activityList->getArrayCopy();
        }

        return [];
    }

    /**
     * @param int $id
     *
     * @return \Jellyfish\ActivityMonitor\ActivityInterface|null
     */
    public function getById(int $id): ?ActivityInterface
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
