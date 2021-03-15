<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

class User implements UserInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $pathRegEx;

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return \Jellyfish\HttpAuthentication\UserInterface
     */
    public function setIdentifier(string $identifier): UserInterface
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return \Jellyfish\HttpAuthentication\UserInterface
     */
    public function setPassword(string $password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPathRegEx(): string
    {
        return $this->pathRegEx;
    }

    /**
     * @param string $pathRegEx
     *
     * @return \Jellyfish\HttpAuthentication\UserInterface
     */
    public function setPathRegEx(string $pathRegEx): UserInterface
    {
        $this->pathRegEx = $pathRegEx;

        return $this;
    }
}
