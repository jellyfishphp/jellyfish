<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;
use Jellyfish\Lock\LockIdentifierGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Lock\LockFactory as SymfonyLockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\SharedLockInterface as SymfonySharedLockInterface;

use function implode;
use function sha1;
use function sprintf;

class LockFactoryTest extends Unit
{
    protected MockObject&SymfonyLockFactory $symfonyLockFactoryMock;

    protected MockObject&LockInterface $symfonyLockMock;

    protected MockObject&LockIdentifierGeneratorInterface $lockIdentifierGeneratorMock;

    protected LockFactory $lockFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonyLockFactoryMock = $this->getMockBuilder(SymfonyLockFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->symfonyLockMock = $this->getMockBuilder(SymfonySharedLockInterface::class)
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
        $lockIdentifierWithoutPrefix = sha1(implode(' ', $lockIdentifierParts));
        $lockIdentifier = sprintf('%s:%s', 'lock', $lockIdentifierWithoutPrefix);

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
