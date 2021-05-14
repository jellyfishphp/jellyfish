<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;
use Jellyfish\Lock\LockInterface;

class LockSymfonyFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\LockSymfony\LockSymfonyFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockSymfonyFactoryMock;

    /**
     * @var \Jellyfish\Lock\LockInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockMock;

    /**
     * @var \Jellyfish\LockSymfony\LockSymfonyFacade
     */
    protected LockSymfonyFacade $lockSymfonyFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->lockSymfonyFactoryMock = $this->getMockBuilder(LockSymfonyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockMock = $this->getMockBuilder(LockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockSymfonyFacade = new LockSymfonyFacade($this->lockSymfonyFactoryMock);
    }

    /**
     * @return void
     */
    public function testCreateLock(): void
    {
        $this->lockSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with(['foo', 'bar'], 3600)
            ->willReturn($this->lockMock);

        static::assertEquals(
            $this->lockMock,
            $this->lockSymfonyFacade->createLock(['foo', 'bar'], 3600)
        );
    }
}
