<?php

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;

class LockIdentifierGeneratorTest extends Unit
{
    /**
     * @var \Jellyfish\Lock\LockIdentifierGeneratorInterface
     */
    protected $lockIdentifierGenerator;

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
        $expectedIdentifierWithoutPrefix = \sha1(\implode(' ', $identifierParts));
        $expectedIdentifier = \sprintf('%s:%s', 'lock', $expectedIdentifierWithoutPrefix);

        $identifier = $this->lockIdentifierGenerator->generate($identifierParts);

        $this->assertEquals($expectedIdentifier, $identifier);
    }
}
