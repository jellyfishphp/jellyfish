<?php

namespace Jellyfish\HttpAuthentication;

interface UserReaderInterface
{
    /**
     * @param string $identifier
     *
     * @return \Jellyfish\HttpAuthentication\UserInterface
     */
    public function getByIdentifier(string $identifier): ?UserInterface;
}
