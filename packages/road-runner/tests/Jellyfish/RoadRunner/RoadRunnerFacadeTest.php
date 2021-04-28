<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Codeception\Test\Unit;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RoadRunnerFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\RoadRunner\RoadRunnerFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $roadRunnerFactoryMock;

    /**
     * @var \Jellyfish\RoadRunner\WorkerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $workerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ServerRequestInterface
     */
    protected $serverRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ResponseInterface
     */
    protected $responseMock;

    /**
     * @var \Jellyfish\RoadRunner\RoadRunnerFacade
     */
    protected $roadRunnerFacade;

    /**
     * @Override
     */
    protected function _before(): void
    {
        parent::_before();

        $this->roadRunnerFactoryMock = $this->getMockBuilder(RoadRunnerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->workerMock = $this->getMockBuilder(WorkerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->roadRunnerFacade = new RoadRunnerFacade($this->roadRunnerFactoryMock);
    }

    /**
     * @return void
     */
    public function testWaitRequest(): void
    {
        $this->roadRunnerFactoryMock->expects(static::atLeastOnce())
            ->method('getWorker')
            ->willReturn($this->workerMock);

        $this->workerMock->expects(static::atLeastOnce())
            ->method('waitRequest')
            ->willReturn($this->serverRequestMock);

        static::assertEquals(
            $this->serverRequestMock,
            $this->roadRunnerFacade->waitRequest()
        );
    }

    /**
     * @return void
     */
    public function testRespond(): void
    {
        $this->roadRunnerFactoryMock->expects(static::atLeastOnce())
            ->method('getWorker')
            ->willReturn($this->workerMock);

        $this->workerMock->expects(static::atLeastOnce())
            ->method('respond')
            ->with($this->responseMock);

        static::assertEquals(
            $this->roadRunnerFacade,
            $this->roadRunnerFacade->respond($this->responseMock)
        );
    }

    /**
     * @return void
     */
    public function testError(): void
    {
        $error = 'Foo';

        $this->roadRunnerFactoryMock->expects(static::atLeastOnce())
            ->method('getWorker')
            ->willReturn($this->workerMock);

        $this->workerMock->expects(static::atLeastOnce())
            ->method('error')
            ->with($error);

        static::assertEquals(
            $this->roadRunnerFacade,
            $this->roadRunnerFacade->error($error)
        );
    }
}
