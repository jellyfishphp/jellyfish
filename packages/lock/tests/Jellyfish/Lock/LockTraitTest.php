<?php

namespace Jellyfish\Lock;

use Codeception\Test\Unit;

class LockTraitTest extends Unit
{
    use LockTrait;

    /**
     * @var \Jellyfish\Lock\LockFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockFactoryMock;

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
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        parent::_before();

        $this->lockFactoryMock = $this->getMockBuilder(LockFactoryInterface::class)
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
        $this->assertTrue($this->acquire($this->identifierParts));
        $this->assertEquals($this, $this->release());
    }

    /**
     * @return void
     */
    public function testAcquireAndReleaseWithLockFactory(): void
    {
        $this->lockFactory = $this->lockFactoryMock;
        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->identifierParts)
            ->willReturn($this->lockMock);


        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $this->assertTrue($this->acquire($this->identifierParts));
        $this->assertEquals($this, $this->release());
    }

    /**
     * @return void
     */
    public function testAcquireWithLockFactoryAndLockedStatus(): void
    {
        $this->lockFactory = $this->lockFactoryMock;
        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->identifierParts)
            ->willReturn($this->lockMock);


        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(false);

        $this->assertFalse($this->acquire($this->identifierParts));
    }
}
