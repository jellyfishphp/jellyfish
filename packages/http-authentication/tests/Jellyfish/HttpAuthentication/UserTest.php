<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Codeception\Test\Unit;

class UserTest extends Unit
{
    /**
     * @var \Jellyfish\HttpAuthentication\UserInterface
     */
    protected $user;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->user = new User();
    }

    /**
     * @return void
     */
    public function testSetAndGetIdentifier(): void
    {
        $identifier = 'foo';

        $this->user->setIdentifier($identifier);

        static::assertEquals($identifier, $this->user->getIdentifier());
    }

    /**
     * @return void
     */
    public function testSetAndGetBody(): void
    {
        $password = 'bar';

        $this->user->setPassword($password);

        static::assertEquals($password, $this->user->getPassword());
    }

    /**
     * @return void
     */
    public function testSetAndGetPathRegEx(): void
    {
        $pathRegEx = '/\/.*/';

        $this->user->setPathRegEx($pathRegEx);

        static::assertEquals($pathRegEx, $this->user->getPathRegEx());
    }
}
