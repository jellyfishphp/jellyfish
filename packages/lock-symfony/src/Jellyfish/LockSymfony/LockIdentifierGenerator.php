<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Jellyfish\Lock\LockIdentifierGeneratorInterface;

use function implode;
use function sha1;
use function sprintf;

class LockIdentifierGenerator implements LockIdentifierGeneratorInterface
{
    protected const IDENTIFIER_PREFIX = 'lock';

    /**
     * @param array $identifierParts
     *
     * @return string
     */
    public function generate(array $identifierParts): string
    {
        $identifierWithoutPrefix = sha1(implode(' ', $identifierParts));

        return sprintf('%s:%s', static::IDENTIFIER_PREFIX, $identifierWithoutPrefix);
    }
}
