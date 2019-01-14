<?php

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;
use Symfony\Component\Lock\LockInterface as SymfonyLockInterface;

class LockTest extends Unit
{
    /**
     * @var \Symfony\Component\Lock\LockInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $symfonyLockMock;

    /**
     * @var \Jellyfish\Lock\LockInterface
     */
    protected $lock;

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonyLockMock = $this->getMockBuilder(SymfonyLockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lock = new Lock($this->symfonyLockMock);
    }

    /**
     * @return void
     */
    public function testAcquire(): void
    {
        $this->symfonyLockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->assertTrue($this->lock->acquire());
    }

    /**
     * @return void
     */
    public function testRelease(): void
    {
        $this->symfonyLockMock->expects($this->atLeastOnce())
            ->method('release');

        $this->lock->release();
    }
}
