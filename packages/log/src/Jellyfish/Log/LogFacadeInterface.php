<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Monolog\Handler\HandlerInterface;

interface LogFacadeInterface
{
    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function emergency(string $message, array $context = array()): LogFacadeInterface;

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function alert(string $message, array $context = array()): LogFacadeInterface;

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function critical(string $message, array $context = array()): LogFacadeInterface;

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function error(string $message, array $context = array()): LogFacadeInterface;

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function warning(string $message, array $context = array()): LogFacadeInterface;

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function notice(string $message, array $context = array()): LogFacadeInterface;

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function info(string $message, array $context = array()): LogFacadeInterface;

    /**
     * @param string $message
     * @param mixed[] $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function debug(string $message, array $context = array()): LogFacadeInterface;

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function log(string $level, string $message, array $context = []): LogFacadeInterface;

    /**
     * @param \Monolog\Handler\HandlerInterface $handler
     *
     * @return \Jellyfish\Log\LogFacadeInterface
     */
    public function addHandler(HandlerInterface $handler): LogFacadeInterface;
}
