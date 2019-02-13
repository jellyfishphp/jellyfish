<?php

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
