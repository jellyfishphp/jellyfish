<?php

declare(strict_types=1);

namespace Jellyfish\LogMonolog;

use Codeception\Test\Unit;
use Monolog\Logger;

class LogMonologFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\LogMonolog\LogMonologFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $logMonologFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    protected $loggerMock;

    /**
     * @var \Jellyfish\LogMonolog\LogMonologFacade
     */
    protected LogMonologFacade $logMonologFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->logMonologFactoryMock = $this->getMockBuilder(LogMonologFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logMonologFacade = new LogMonologFacade($this->logMonologFactoryMock);
    }

    /**
     * @return void
     */
    public function testGetLogger(): void
    {
        $this->logMonologFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        static::assertEquals(
            $this->loggerMock,
            $this->logMonologFacade->getLogger()
        );
    }
}
