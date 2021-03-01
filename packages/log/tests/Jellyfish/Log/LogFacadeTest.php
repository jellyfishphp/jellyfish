<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Codeception\Test\Unit;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class LogFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\Log\LogFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $logFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Monolog\Logger
     */
    protected $loggerMock;

    /**
     * @var \Monolog\Handler\HandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $handlerMock;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var \Jellyfish\Log\LogFacade
     */
    protected $logFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->logFactoryMock = $this->getMockBuilder(LogFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->handlerMock = $this->getMockBuilder(HandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->message = 'foo bar';

        $this->context = [];

        $this->logFacade = new LogFacade($this->logFactoryMock);
    }

    /**
     * @return void
     */
    public function testEmergency(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('emergency')
            ->with($this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->emergency($this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testWarning(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('warning')
            ->with($this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->warning($this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testError(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('error')
            ->with($this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->error($this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testLog(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('log')
            ->with(LogConstants::LOG_LEVEL_INFO, $this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->log(LogConstants::LOG_LEVEL_INFO, $this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testInfo(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('info')
            ->with($this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->info($this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testAlert(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('alert')
            ->with($this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->alert($this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testDebug(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('debug')
            ->with($this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->debug($this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testNotice(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('notice')
            ->with($this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->notice($this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testCritical(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('critical')
            ->with($this->message, $this->context);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->critical($this->message, $this->context)
        );
    }

    /**
     * @return void
     */
    public function testAddHandler(): void
    {
        $this->logFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('pushHandler')
            ->with($this->handlerMock);

        static::assertEquals(
            $this->logFacade,
            $this->logFacade->addHandler($this->handlerMock)
        );
    }
}
