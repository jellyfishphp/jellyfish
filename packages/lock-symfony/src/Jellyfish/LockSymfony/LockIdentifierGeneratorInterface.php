<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

interface LockIdentifierGeneratorInterface
{
    /**
     * @param string[] $identifierParts
     *
     * @return string
     */
    public function generate(array $identifierParts): string;
}
