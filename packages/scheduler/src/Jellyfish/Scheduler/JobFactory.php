<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Cron\CronExpression;
use DateTime;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Process\ProcessInterface;

class JobFactory implements JobFactoryInterface
{
    /**
     * @var \Jellyfish\Process\ProcessFactoryInterface
     */
    protected $processFactory;

    /**
     * @var \Jellyfish\Scheduler\CronExpressionFactoryInterface
     */
    protected $cronExpressionFactory;

    /**
     * JobFactory constructor.
     *
     * @param  \Jellyfish\Process\ProcessFactoryInterface  $processFactory
     * @param  \Jellyfish\Scheduler\CronExpressionFactoryInterface  $cronExpressionFactory
     */
    public function __construct(
        ProcessFactoryInterface $processFactory,
        CronExpressionFactoryInterface $cronExpressionFactory
    ) {
        $this->processFactory = $processFactory;
        $this->cronExpressionFactory = $cronExpressionFactory;
    }

    /**
     * @param  array  $command
     * @param  string  $cronExpression
     *
     * @return \Jellyfish\Scheduler\JobInterface
     */
    public function create(array $command, string $cronExpression): JobInterface
    {
        return new Job($this->createProcess($command), $this->createExpression($cronExpression));
    }

    /**
     * @param  string  $expression
     *
     * @return \Cron\CronExpression
     */
    protected function createExpression(string $expression): CronExpression
    {
        return $this->cronExpressionFactory->create($expression);
    }

    /**
     * @param  array  $command
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    protected function createProcess(array $command): ProcessInterface
    {
        return $this->processFactory->create($command);
    }
}
