<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

interface UserInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param string $identifier
     *
     * @return \Jellyfish\HttpAuthentication\UserInterface
     */
    public function setIdentifier(string $identifier): UserInterface;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @param string $password
     *
     * @return \Jellyfish\HttpAuthentication\UserInterface
     */
    public function setPassword(string $password): UserInterface;

    /**
     * @return string
     */
    public function getPathRegEx(): string;

    /**
     * @param string $pathRegEx
     *
     * @return \Jellyfish\HttpAuthentication\UserInterface
     */
    public function setPathRegEx(string $pathRegEx): UserInterface;
}
