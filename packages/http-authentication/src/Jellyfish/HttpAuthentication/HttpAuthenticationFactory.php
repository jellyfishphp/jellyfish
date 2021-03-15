<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

class HttpAuthenticationFactory
{
    /**
     * @var string
     */
    protected $appDir;

    /**
     * @var \Jellyfish\HttpAuthentication\AuthenticationInterface
     */
    protected $authentication;

    /**
     * @param string $appDir
     */
    public function __construct(string $appDir)
    {
        $this->appDir = $appDir;
    }

    /**
     * @return \Jellyfish\HttpAuthentication\AuthenticationInterface
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    public function getAuthentication(): AuthenticationInterface
    {
        if ($this->authentication === null) {
            $this->authentication = new BasicAuthentication($this->createUserReader());
        }

        return $this->authentication;
    }

    /**
     * @return \Jellyfish\HttpAuthentication\UserReaderInterface
     *
     */
    protected function createUserReader(): UserReaderInterface
    {
        return new UserReader($this->appDir);
    }
}
