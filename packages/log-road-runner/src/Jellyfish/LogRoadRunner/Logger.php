<?php

namespace Jellyfish\LogRoadRunner;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RoadRunner\Logger\Logger as RoadRunnerLogger;

/**
 * @codeCoverageIgnore
 */
class Logger implements LoggerInterface
{
    protected const LOG_LEVEL_RANK = [
        LogLevel::DEBUG => 1,
        LogLevel::INFO => 2,
        LogLevel::NOTICE => 3,
        LogLevel::WARNING => 4,
        LogLevel::ERROR => 5,
        LogLevel::CRITICAL => 6,
        LogLevel::ALERT => 7,
        LogLevel::EMERGENCY => 8,
    ];

    /**
     * @var \RoadRunner\Logger\Logger
     */
    protected RoadRunnerLogger $roadRunnerLogger;

    /**
     * @var string
     */
    protected string $level;

    /**
     * @param \RoadRunner\Logger\Logger $roadRunnerLogger
     * @param string $level
     */
    public function __construct(
        RoadRunnerLogger $roadRunnerLogger,
        string $level
    ) {
        $this->roadRunnerLogger = $roadRunnerLogger;
        $this->level = $level;
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = array()): void
    {
        if (!$this->canLog(LogLevel::EMERGENCY)) {
            return;
        }

        $this->roadRunnerLogger->error($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = array()): void
    {
        if (!$this->canLog(LogLevel::ALERT)) {
            return;
        }

        $this->roadRunnerLogger->error($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = array()): void
    {
        if (!$this->canLog(LogLevel::CRITICAL)) {
            return;
        }

        $this->roadRunnerLogger->error($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = array()): void
    {
        if (!$this->canLog(LogLevel::ERROR)) {
            return;
        }

        $this->roadRunnerLogger->error($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = array()): void
    {
        if (!$this->canLog(LogLevel::WARNING)) {
            return;
        }

        $this->roadRunnerLogger->warning($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = array()): void
    {
        if (!$this->canLog(LogLevel::NOTICE)) {
            return;
        }

        $this->roadRunnerLogger->warning($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = array()): void
    {
        if (!$this->canLog(LogLevel::INFO)) {
            return;
        }

        $this->roadRunnerLogger->info($message);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = array()): void
    {
        if (!$this->canLog(LogLevel::DEBUG)) {
            return;
        }

        $this->roadRunnerLogger->debug($message);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array()): void
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
                $this->roadRunnerLogger->error($message);
                break;
            case LogLevel::WARNING:
            case LogLevel::NOTICE:
                $this->roadRunnerLogger->warning($message);
                break;
            case LogLevel::INFO:
                $this->roadRunnerLogger->info($message);
                break;
            default:
                $this->roadRunnerLogger->debug($message);
        }
    }

    /**
     * @param string $maxLogLevel
     *
     * @return bool
     */
    protected function canLog(string $maxLogLevel): bool
    {
        if (!isset(static::LOG_LEVEL_RANK[$maxLogLevel], static::LOG_LEVEL_RANK[$this->level])) {
            return false;
        }

        $maxLogLevelRank = static::LOG_LEVEL_RANK[$maxLogLevel];
        $currentLogLevelRank = static::LOG_LEVEL_RANK[$this->level];

        return $currentLogLevelRank <= $maxLogLevelRank;
    }
}
