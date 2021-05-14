<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use ArrayObject;
use Codeception\Test\Unit;
use Exception;
use Generated\Transfer\Pm2\Activity;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Process\ProcessInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class Pm2Test extends Unit
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processFacadeMock;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerFacadeMock;

    /**
     * @var \Jellyfish\Process\ProcessInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processMock;

    /**
     * @var \Generated\Transfer\Pm2\Activity[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected array $pm2ActivityMocks;

    /**
     * @var \Jellyfish\ActivityMonitor\Pm2
     */
    protected Pm2 $pm2;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->processFacadeMock = $this->getMockBuilder(ProcessFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processMock = $this->getMockBuilder(ProcessInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pm2ActivityMocks = [
            $this->getMockBuilder(Activity::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        $this->pm2 = new Pm2($this->processFacadeMock, $this->serializerFacadeMock);
    }

    /**
     * @return void
     */
    public function testGetActivities(): void
    {
        $output = '[{...}]';

        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with([Pm2::PM2_CLI, Pm2::ARGUMENT_JLIST])
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('wait')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getExitCode')
            ->willReturn(0);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getOutput')
            ->willReturn($output);

        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('deserialize')
            ->with($output, sprintf('%s[]', Activity::class), 'json')
            ->willReturn(new ArrayObject($this->pm2ActivityMocks));

        static::assertEquals(
            $this->pm2ActivityMocks,
            $this->pm2->getActivities()
        );
    }

    /**
     * @return void
     */
    public function testGetActivitiesWithError(): void
    {
        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with([Pm2::PM2_CLI, Pm2::ARGUMENT_JLIST])
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('wait')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getExitCode')
            ->willReturn(1);

        try {
            $this->pm2->getActivities();
            static::fail();
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    public function testStartActivity(): void
    {
        $activityId = 1;

        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with([Pm2::PM2_CLI, Pm2::ARGUMENT_START, $activityId])
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('wait')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getExitCode')
            ->willReturn(0);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getOutput')
            ->willReturn('Foo');

        static::assertEquals(
            $this->pm2,
            $this->pm2->startActivity($activityId)
        );
    }

    /**
     * @return void
     */
    public function testStopActivity(): void
    {
        $activityId = 1;

        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with([Pm2::PM2_CLI, Pm2::ARGUMENT_STOP, $activityId])
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('wait')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getExitCode')
            ->willReturn(0);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getOutput')
            ->willReturn('Foo');

        static::assertEquals(
            $this->pm2,
            $this->pm2->stopActivity($activityId)
        );
    }

    /**
     * @return void
     */
    public function testRestartActivity(): void
    {
        $activityId = 1;

        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with([Pm2::PM2_CLI, Pm2::ARGUMENT_RESTART, $activityId])
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('wait')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getExitCode')
            ->willReturn(0);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getOutput')
            ->willReturn('Foo');

        static::assertEquals(
            $this->pm2,
            $this->pm2->restartActivity($activityId)
        );
    }

    /**
     * @return void
     */
    public function testRestartAllActivities(): void
    {
        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with([Pm2::PM2_CLI, Pm2::ARGUMENT_RESTART, Pm2::ARGUMENT_ALL])
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('wait')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getExitCode')
            ->willReturn(0);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getOutput')
            ->willReturn('Foo');

        static::assertEquals(
            $this->pm2,
            $this->pm2->restartAllActivities()
        );
    }

    /**
     * @return void
     */
    public function testStopAllActivities(): void
    {
        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with([Pm2::PM2_CLI, Pm2::ARGUMENT_STOP, Pm2::ARGUMENT_ALL])
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('wait')
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getExitCode')
            ->willReturn(0);

        $this->processMock->expects(static::atLeastOnce())
            ->method('getOutput')
            ->willReturn('Foo');

        static::assertEquals(
            $this->pm2,
            $this->pm2->stopAllActivities()
        );
    }
}
