<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;
use Jellyfish\Lock\LockIdentifierGeneratorInterface;
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
     * @var \Jellyfish\Lock\LockIdentifierGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockIdentifierGeneratorMock;

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

        $this->lockIdentifierGeneratorMock = $this->getMockBuilder(LockIdentifierGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockFactory = new LockFactory(
            $this->symfonyLockFactoryMock,
            $this->lockIdentifierGeneratorMock
        );
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $lockIdentifierParts = ['x', 'y'];
        $lockIdentifierWithoutPrefix = \sha1(\implode(' ', $lockIdentifierParts));
        $lockIdentifier = \sprintf('%s:%s', 'lock', $lockIdentifierWithoutPrefix);


        $this->lockIdentifierGeneratorMock->expects($this->atLeastOnce())
            ->method('generate')
            ->with($lockIdentifierParts)
            ->willReturn($lockIdentifier);

        $this->symfonyLockFactoryMock->expects($this->atLeastOnce())
            ->method('createLock')
            ->with($lockIdentifier, 360.0)
            ->willReturn($this->symfonyLockMock);

        $this->assertInstanceOf(Lock::class, $this->lockFactory->create($lockIdentifierParts, 360.0));
    }
}
