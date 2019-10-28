<?php

declare(strict_types=1);

namespace Jellyfish\Lock;

interface LockIdentifierGeneratorInterface
{
    /**
     * @param array $identifierParts
     *
     * @return string
     */
    public function generate(array $identifierParts): string;
}
