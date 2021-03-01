<?php

declare(strict_types=1);

namespace Jellyfish\Lock;

use Codeception\Test\Unit;

class LockTraitTest extends Unit
{
    use LockTrait;

    /**
     * @var \Jellyfish\Lock\LockFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockFacadeMock;

    /**
     * @var \Jellyfish\Lock\LockInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockMock;

    /**
     * @var array
     */
    protected $identifierParts = ['test1', 'test2'];

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->lockFacadeMock = $this->getMockBuilder(LockFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockMock = $this->getMockBuilder(LockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     */
    public function testAcquireAndReleaseWithNullLockFactory(): void
    {
        static::assertTrue($this->acquire($this->identifierParts));
        $this->release();
    }

    /**
     * @return void
     */
    public function testAcquireAndReleaseWithLockFactory(): void
    {
        $this->lockFacade = $this->lockFacadeMock;

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->identifierParts)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        static::assertTrue($this->acquire($this->identifierParts));
        $this->release();
    }

    /**
     * @return void
     */
    public function testAcquireWithLockFactoryAndLockedStatus(): void
    {
        $this->lockFacade = $this->lockFacadeMock;

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->identifierParts)
            ->willReturn($this->lockMock);


        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(false);

        static::assertFalse($this->acquire($this->identifierParts));
    }
}
