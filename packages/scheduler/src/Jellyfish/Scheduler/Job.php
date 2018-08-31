<?php

namespace Jellyfish\Scheduler;

use Cron\CronExpression;
use DateTime;

class Job implements JobInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $preparedCommand;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var \Cron\CronExpression
     */
    protected $cronExpression;

    /**
     * @var string
     */
    protected $pathToLockFile;

    /**
     * @param string $command
     * @param \Cron\CronExpression $cronExpression
     * @param string $tempDir
     */
    public function __construct(
        string $command,
        CronExpression $cronExpression,
        string $tempDir
    ) {
        $this->id = \sha1($command);
        $this->command = $command;
        $this->cronExpression = $cronExpression;
        $this->pathToLockFile = rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->id;
        $this->preparedCommand = '(' . $this->command . ' ; rm ' . $this->pathToLockFile . ') > /dev/null 2>&1 &';
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return \Cron\CronExpression
     */
    public function getCronExpression(): CronExpression
    {
        return $this->cronExpression;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    protected function isDue(DateTime $dateTime): bool
    {
        return $this->cronExpression->isDue($dateTime);
    }

    /**
     * @return bool
     */
    protected function isLocked(): bool
    {
        return file_exists($this->pathToLockFile);
    }

    /**
     * @return void
     */
    protected function lock(): void
    {
        touch($this->pathToLockFile);
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return void
     */
    public function run(?DateTime $dateTime = null): void
    {
        if ($dateTime === null) {
            $dateTime = new DateTime();
        }

        if ($this->isLocked()) {
            return;
        }

        if (!$this->isDue($dateTime)) {
            return;
        }

        $this->lock();
        exec($this->preparedCommand);
    }
}
