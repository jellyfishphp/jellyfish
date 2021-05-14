<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Codeception\Test\Unit;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;
use Spiral\RoadRunner\WorkerInterface as RoadRunnerWorkerInterface;

class WorkerTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spiral\RoadRunner\Http\PSR7WorkerInterface
     */
    protected $psr7WorkerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spiral\RoadRunner\WorkerInterface
     */
    protected $roadRunnerWorkerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ServerRequestInterface
     */
    protected $serverRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ResponseInterface
     */
    protected $responseMock;

    /**
     * @var \Jellyfish\RoadRunner\Worker
     */
    protected Worker $worker;


    /**
     * @Override
     */
    protected function _before(): void
    {
        parent::_before();

        $this->psr7WorkerMock = $this->getMockBuilder(PSR7WorkerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->roadRunnerWorkerMock = $this->getMockBuilder(RoadRunnerWorkerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->worker = new Worker($this->psr7WorkerMock);
    }

    /**
     * @return void
     */
    public function testWaitRequest(): void
    {
        $this->psr7WorkerMock->expects(static::atLeastOnce())
            ->method('waitRequest')
            ->willReturn($this->serverRequestMock);

        static::assertEquals(
            $this->serverRequestMock,
            $this->worker->waitRequest()
        );
    }

    /**
     * @return void
     */
    public function testRespond(): void
    {
        $this->psr7WorkerMock->expects(static::atLeastOnce())
            ->method('respond')
            ->with($this->responseMock);

        static::assertEquals(
            $this->worker,
            $this->worker->respond($this->responseMock)
        );
    }

    /**
     * @return void
     */
    public function testError(): void
    {
        $error = 'Foo';

        $this->roadRunnerWorkerMock->expects(static::atLeastOnce())
            ->method('error')
            ->with($error);

        $this->psr7WorkerMock->expects(static::atLeastOnce())
            ->method('getWorker')
            ->willReturn($this->roadRunnerWorkerMock);

        static::assertEquals(
            $this->worker,
            $this->worker->error($error)
        );
    }
}
