<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockIdentifierGeneratorInterface;
use Jellyfish\Lock\LockInterface;
use Symfony\Component\Lock\Factory as SymfonyLockFactory;

class LockFactory implements LockFactoryInterface
{
    /**
     * @var \Symfony\Component\Lock\Factory
     */
    protected $symfonyLockFactory;

    /**
     * @var \Jellyfish\Lock\LockIdentifierGeneratorInterface
     */
    protected $lockIdentifierGenerator;

    /**
     * @param \Symfony\Component\Lock\Factory $symfonyLockFactory
     * @param \Jellyfish\Lock\LockIdentifierGeneratorInterface $lockIdentifierGenerator
     */
    public function __construct(
        SymfonyLockFactory $symfonyLockFactory,
        LockIdentifierGeneratorInterface $lockIdentifierGenerator
    ) {
        $this->symfonyLockFactory = $symfonyLockFactory;
        $this->lockIdentifierGenerator = $lockIdentifierGenerator;
    }

    /**
     * @param array $identifierParts
     * @param float $ttl
     *
     * @return \Jellyfish\Lock\LockInterface
     */
    public function create(array $identifierParts, float $ttl): LockInterface
    {
        $identifier = $this->lockIdentifierGenerator->generate($identifierParts);

        $symfonyLock = $this->symfonyLockFactory->createLock($identifier, $ttl, false);

        return new Lock($symfonyLock);
    }
}
