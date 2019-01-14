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
    public function testCreateIdentifier(): void
    {
        $expectedResult = \sha1(\implode(' ', $this->identifierParts));

        $this->assertEquals($expectedResult, $this->createIdentifier($this->identifierParts));
    }

    /**
     * @return void
     */
    public function testAcquireAndReleaseWithNullLockFactory(): void
    {
        $lockIdentifier = $this->createIdentifier($this->identifierParts);

        $this->assertTrue($this->acquire($lockIdentifier));
        $this->assertEquals($this, $this->release());
    }

    /**
     * @return void
     */
    public function testAcquireAndReleaseWithLockFactory(): void
    {
        $lockIdentifier = $this->createIdentifier($this->identifierParts);

        $this->lockFactory = $this->lockFactoryMock;
        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($lockIdentifier)
            ->willReturn($this->lockMock);


        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $this->assertTrue($this->acquire($lockIdentifier));
        $this->assertEquals($this, $this->release());
    }

    /**
     * @return void
     */
    public function testAcquireWithLockFactoryAndLockedStatus(): void
    {
        $lockIdentifier = $this->createIdentifier($this->identifierParts);

        $this->lockFactory = $this->lockFactoryMock;
        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($lockIdentifier)
            ->willReturn($this->lockMock);


        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(false);

        $this->assertFalse($this->acquire($lockIdentifier));
    }
}
