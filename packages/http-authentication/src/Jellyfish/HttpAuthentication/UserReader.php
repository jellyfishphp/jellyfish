<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use ArrayObject;

use function file_exists;

class UserReader implements UserReaderInterface
{
    protected const USERS_FILE = 'users.php';

    /**
     * @var \ArrayObject<string, \Jellyfish\HttpAuthentication\UserInterface>
     */
    protected ArrayObject $users;

    /**
     * @param string $appDir
     */
    public function __construct(string $appDir)
    {
        $this->initialize($appDir);
    }

    /**
     * @param string $appDir
     *
     * @return \Jellyfish\HttpAuthentication\UserReaderInterface
     */
    protected function initialize(string $appDir): UserReaderInterface
    {
        $users = new ArrayObject();
        $pathToUsersFile = $appDir . static::USERS_FILE;

        if (file_exists($pathToUsersFile)) {
            include $pathToUsersFile;
        }

        $this->users = $users;

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return \Jellyfish\HttpAuthentication\UserInterface
     */
    public function getByIdentifier(string $identifier): ?UserInterface
    {
        if (!$this->users->offsetExists($identifier)) {
            return null;
        }

        return $this->users->offsetGet($identifier);
    }
}
