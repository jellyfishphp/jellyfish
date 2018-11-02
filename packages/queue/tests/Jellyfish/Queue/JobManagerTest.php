<?php

namespace Jellyfish\Queue;

use Codeception\Test\Unit;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Process\ProcessInterface;
use Jellyfish\Queue\Command\RunJobCommand;

class JobManagerTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\ClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $clientMock;

    /**
     * @var \Jellyfish\Process\ProcessFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processFactoryMock;

    /**
     * @var \Jellyfish\Process\ProcessInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processMock;

    /**
     * @var \Jellyfish\Queue\JobInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jobMock;

    /**
     * @var \Jellyfish\Queue\JobManagerInterface
     */
    protected $jobManager;

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        $this->clientMock = $this->getMockForAbstractClass(ClientInterface::class);
        $this->processFactoryMock = $this->getMockForAbstractClass(ProcessFactoryInterface::class);
        $this->processMock = $this->getMockForAbstractClass(ProcessInterface::class);
        $this->jobMock = $this->getMockForAbstractClass(JobInterface::class);

        $this->jobManager = new JobManager($this->clientMock, $this->processFactoryMock);
    }

    /**
     * @return void
     */
    public function testGetNonExistingJob(): void
    {
        $this->assertEquals(null, $this->jobManager->getJob('test'));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSetAndGetJob(): void
    {
        $this->jobManager->setJob('test', $this->jobMock);

        $this->assertEquals($this->jobMock, $this->jobManager->getJob('test'));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSetJobAndGetAllJobs(): void
    {
        $this->assertCount(0, $this->jobManager->getAllJobs());
        $this->jobManager->setJob('test', $this->jobMock);
        $this->assertCount(1, $this->jobManager->getAllJobs());
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSetJobsAndGetQueueNames(): void
    {
        $this->assertNotContains('test', $this->jobManager->getQueueNames());
        $this->jobManager->setJob('test', $this->jobMock);
        $this->assertContains('test', $this->jobManager->getQueueNames());
    }

    /**
     * @return void
     */
    public function testUnsetNonExistingJob(): void
    {
        $countBeforeUnset = count($this->jobManager->getAllJobs());

        $this->jobManager->unsetJob('test');
        $this->assertCount($countBeforeUnset, $this->jobManager->getAllJobs());
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSetAndUnsetJob(): void
    {
        $queueName = 'test';

        $this->jobManager->setJob($queueName, $this->jobMock);
        $this->assertContains($queueName, $this->jobManager->getQueueNames());

        $this->jobManager->unsetJob($queueName);
        $this->assertNotContains($queueName, $this->jobManager->getQueueNames());
    }

    /**
     * @return void
     */
    public function testRunNonExistingJob(): void
    {
        $queueName = 'test';

        $this->clientMock->expects($this->never())
            ->method('receiveMessage')
            ->with($queueName)
            ->willReturn('msgBody');

        $this->assertEquals($this->jobManager, $this->jobManager->runJob($queueName));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testRunJob(): void
    {
        $queueName = 'test';
        $message = 'msg';

        $this->jobManager->setJob($queueName, $this->jobMock);

        $this->clientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with($queueName)
            ->willReturn($message);

        $this->jobMock->expects($this->atLeastOnce())
            ->method('run')
            ->with($message)
            ->willReturn($this->jobMock);

        $this->assertEquals($this->jobManager, $this->jobManager->runJob($queueName));
    }

    /**
     * @return void
     */
    public function testRunJobAsProcess(): void
    {
        $queueName = 'test';

        $this->jobManager->setJob($queueName, $this->jobMock);

        $this->processFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with(['', RunJobCommand::NAME, $queueName])
            ->willReturn($this->processMock);

        $this->processMock->expects($this->atLeastOnce())
            ->method('isLocked')
            ->willReturn(false);

        $this->processMock->expects($this->atLeastOnce())
            ->method('start');

        $this->assertEquals($this->jobManager, $this->jobManager->runJobAsProcess($queueName));
    }
}
