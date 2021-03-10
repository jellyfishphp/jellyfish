<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Jellyfish\Config\ConfigFacadeInterface;

class HttpAuthenticationFactory
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface
     */
    protected $configFacade;

    /**
     * @var \Jellyfish\HttpAuthentication\AuthenticationInterface
     */
    protected $authentication;

    /**
     * @param \Jellyfish\Config\ConfigFacadeInterface $configFacade
     */
    public function __construct(ConfigFacadeInterface $configFacade)
    {
        $this->configFacade = $configFacade;
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
            $this->authentication = new BasicAuthentication($this->createUser());
        }

        return $this->authentication;
    }

    /**
     * @return \Jellyfish\HttpAuthentication\UserInterface
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    protected function createUser(): UserInterface
    {
        $identifier = $this->configFacade->get(
            HttpAuthenticationConstants::USER_IDENTIFIER,
            HttpAuthenticationConstants::DEFAULT_USER_IDENTIFIER
        );

        $password = $this->configFacade->get(
            HttpAuthenticationConstants::USER_PASSWORD,
            HttpAuthenticationConstants::DEFAULT_USER_PASSWORD
        );

        return (new User())->setIdentifier($identifier)
            ->setPassword($password);
    }
}
