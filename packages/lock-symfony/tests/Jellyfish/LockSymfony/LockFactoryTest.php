<?php

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;
use Symfony\Component\Lock\Factory as SymfonyLockFactory;
use Symfony\Component\Lock\LockInterface as SymfonyLockInterface;

class LockFactoryTest extends Unit
{
    /**
     * @var \Symfony\Component\Lock\Factory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $symfonyLockFactoryMock;

    /**
     * @var \Jellyfish\Lock\LockFactoryInterface
     */
    protected $lockFactory;

    /**
     * @var \Symfony\Component\Lock\LockInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $symfonyLockMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonyLockFactoryMock = $this->getMockBuilder(SymfonyLockFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->symfonyLockMock = $this->getMockBuilder(SymfonyLockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockFactory = new LockFactory($this->symfonyLockFactoryMock);
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $lockIdentifier = \sha1('test1 test2');

        $this->symfonyLockFactoryMock->expects($this->atLeastOnce())
            ->method('createLock')
            ->with($lockIdentifier, 360.0)
            ->willReturn($this->symfonyLockMock);

        $this->assertInstanceOf(Lock::class, $this->lockFactory->create($lockIdentifier, 360.0));
    }
}
