<?php

declare(strict_types=1);

namespace Jellyfish\LogRoadRunner;

use Codeception\Test\Unit;

class LogRoadRunnerFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\LogRoadRunner\LogRoadRunnerFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $logRoadRunnerFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    protected $loggerMock;

    /**
     * @var \Jellyfish\LogRoadRunner\LogRoadRunnerFacade
     */
    protected LogRoadRunnerFacade $logRoadRunnerFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->logRoadRunnerFactoryMock = $this->getMockBuilder(LogRoadRunnerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logRoadRunnerFacade = new LogRoadRunnerFacade($this->logRoadRunnerFactoryMock);
    }

    /**
     * @return void
     */
    public function testGetLogger(): void
    {
        $this->logRoadRunnerFactoryMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        static::assertEquals(
            $this->loggerMock,
            $this->logRoadRunnerFacade->getLogger()
        );
    }
}
