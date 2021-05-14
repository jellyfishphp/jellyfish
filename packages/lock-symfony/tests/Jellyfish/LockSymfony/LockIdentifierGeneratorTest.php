<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;

use function implode;
use function sha1;
use function sprintf;

class LockIdentifierGeneratorTest extends Unit
{
    /**
     * @var \Jellyfish\LockSymfony\LockIdentifierGeneratorInterface
     */
    protected LockIdentifierGeneratorInterface $lockIdentifierGenerator;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->lockIdentifierGenerator = new LockIdentifierGenerator();
    }

    /**
     * @return void
     */
    public function testGenerate(): void
    {
        $identifierParts = ['x', 'y'];
        $expectedIdentifierWithoutPrefix = sha1(implode(' ', $identifierParts));
        $expectedIdentifier = sprintf('%s:%s', 'lock', $expectedIdentifierWithoutPrefix);

        $identifier = $this->lockIdentifierGenerator->generate($identifierParts);

        static::assertEquals($expectedIdentifier, $identifier);
    }
}
