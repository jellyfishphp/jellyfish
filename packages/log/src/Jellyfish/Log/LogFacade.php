<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class LogFacade implements LogFacadeInterface
{
    /**
     * @var \Jellyfish\Log\LogFactory
     */
    protected $logFactory;

    /**
     * @param \Jellyfish\Log\LogFactory $logFactory
     */
    public function __construct(LogFactory $logFactory)
    {
        $this->logFactory = $logFactory;
    }

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function emergency(string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->emergency($message, $context);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function alert(string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->alert($message, $context);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function critical(string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->critical($message, $context);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function error(string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->error($message, $context);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function warning(string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->warning($message, $context);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function notice(string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->notice($message, $context);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function info(string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->info($message, $context);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function debug(string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->debug($message, $context);

        return $this;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function log(string $level, string $message, array $context = []): LogFacadeInterface
    {
        $this->logFactory->getLogger()->log($level, $message, $context);

        return $this;
    }

    /**
     * @param \Monolog\Handler\HandlerInterface $handler
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function addHandler(HandlerInterface $handler): LogFacadeInterface
    {
        $logger = $this->logFactory->getLogger();

        if ($logger instanceof Logger) {
            $logger->pushHandler($handler);
        }

        return $this;
    }
}
