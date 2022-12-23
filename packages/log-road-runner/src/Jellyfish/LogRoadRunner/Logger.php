<?php

namespace Jellyfish\LogRoadRunner;

use DateTime;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RoadRunner\Logger\Logger as RoadRunnerLogger;

use function get_class;
use function is_object;
use function is_resource;
use function is_scalar;
use function json_encode;
use function method_exists;
use function sprintf;

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

        $message = $this->interpolate($message, $context);

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

        $message = $this->interpolate($message, $context);

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

        $message = $this->interpolate($message, $context);

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

        $message = $this->interpolate($message, $context);

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

        $message = $this->interpolate($message, $context);

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

        $message = $this->interpolate($message, $context);

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

        $message = $this->interpolate($message, $context);

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

        $message = $this->interpolate($message, $context);

        $this->roadRunnerLogger->info($message);
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

    /**
     * @param string $message
     * @param array $context
     *
     * @return string
     */
    protected function interpolate(string $message, array $context = array()): string
    {
        $replacements = array();

        foreach ($context as $key => $value) {
            $replacementKey = sprintf('{{%s}}', $key);

            if ($value === null || is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $replacements[$replacementKey] = $value;
            } elseif ($value instanceof DateTimeInterface) {
                $replacements[$replacementKey] = $value->format(DateTime::RFC3339);
            } elseif (is_object($value)) {
                $replacements[$replacementKey] = '{object ' . get_class($value) . '}';
            } elseif (is_resource($value)) {
                $replacements[$replacementKey] = '{resource}';
            } else {
                $replacements[$replacementKey] = json_encode($value);
            }
        }

        return strtr($message, $replacements);
    }
}
