<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use ArrayObject;
use Generated\Transfer\Pm2\Activity as Pm2Activity;
use Generated\Transfer\ActivityMonitor\Activity;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

use function sprintf;

class ActivityReader implements ActivityReaderInterface
{
    public const PM2_CLI = 'pm2';
    public const ARGUMENT_JLIST = 'jlist';

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMapperInterface
     */
    protected $activityMapper;

    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected $processFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacade;

    /**
     * @param \Jellyfish\ActivityMonitor\ActivityMapperInterface $activityMapper
     * @param \Jellyfish\Process\ProcessFacadeInterface $processFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        ActivityMapperInterface $activityMapper,
        ProcessFacadeInterface $processFacade,
        SerializerFacadeInterface $serializerFacade
    ) {
        $this->activityMapper = $activityMapper;
        $this->processFacade = $processFacade;
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @return \Generated\Transfer\ActivityMonitor\Activity[]
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

        $pm2Activities = $this->serializerFacade->deserialize(
            $process->getOutput(),
            sprintf('%s[]', Pm2Activity::class),
            'json'
        );

        if (!($pm2Activities instanceof ArrayObject)) {
            return [];
        }

        return $this->activityMapper->mapPm2ActivitiesToActivities($pm2Activities->getArrayCopy());
    }

    /**
     * @param int $id
     *
     * @return \Generated\Transfer\ActivityMonitor\Activity|null
     *
     * @throws \Jellyfish\Process\Exception\AlreadyStartedException
     * @throws \Jellyfish\Process\Exception\NotStartedException
     * @throws \Jellyfish\Process\Exception\NotTerminatedException
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
