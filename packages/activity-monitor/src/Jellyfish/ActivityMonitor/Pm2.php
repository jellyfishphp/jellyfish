<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use ArrayObject;
use Generated\Transfer\Pm2\Activity;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;
use RuntimeException;

use function sprintf;

class Pm2 implements Pm2Interface
{
    public const PM2_CLI = 'pm2';

    public const ARGUMENT_JLIST = 'jlist';
    public const ARGUMENT_START = 'start';
    public const ARGUMENT_STOP = 'stop';
    public const ARGUMENT_RESTART = 'restart';
    public const ARGUMENT_ALL = 'all';

    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected ProcessFacadeInterface $processFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected SerializerFacadeInterface $serializerFacade;

    /**
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
     * @return \Generated\Transfer\Pm2\Activity[]
     */
    public function getActivities(): array
    {
        $output = $this->runCommandAsProcess([static::PM2_CLI, static::ARGUMENT_JLIST]);

        $pm2Activities = $this->serializerFacade->deserialize(
            $output,
            sprintf('%s[]', Activity::class),
            'json'
        );

        if (!($pm2Activities instanceof ArrayObject)) {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        return $pm2Activities->getArrayCopy();
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function startActivity(int $activityId): Pm2Interface
    {
        $this->runCommandAsProcess([static::PM2_CLI, static::ARGUMENT_START, (string)$activityId]);

        return $this;
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function restartActivity(int $activityId): Pm2Interface
    {
        $this->runCommandAsProcess([static::PM2_CLI, static::ARGUMENT_RESTART, (string)$activityId]);

        return $this;
    }

    /**
     * @param int $activityId
     *
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function stopActivity(int $activityId): Pm2Interface
    {
        $this->runCommandAsProcess([static::PM2_CLI, static::ARGUMENT_STOP, (string)$activityId]);

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function restartAllActivities(): Pm2Interface
    {
        $this->runCommandAsProcess([static::PM2_CLI, static::ARGUMENT_RESTART, static::ARGUMENT_ALL]);

        return $this;
    }

    /**
     * @return \Jellyfish\ActivityMonitor\Pm2Interface
     */
    public function stopAllActivities(): Pm2Interface
    {
        $this->runCommandAsProcess([static::PM2_CLI, static::ARGUMENT_STOP, static::ARGUMENT_ALL]);

        return $this;
    }

    /**
     * @param string[] $command
     *
     * @return string
     */
    protected function runCommandAsProcess(array $command): string
    {
        $process = $this->processFacade->createProcess($command)
            ->start()
            ->wait();

        if ($process->getExitCode() !== 0) {
            throw new RuntimeException($process->getOutput(), $process->getExitCode());
        }

        return $process->getOutput();
    }
}
