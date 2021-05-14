<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;

class UserReaderTest extends Unit
{
    /**
     * @var \Jellyfish\HttpAuthentication\UserReader
     */
    protected UserReader $userReader;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $rootDir = vfsStream::setup('root', null, [
            'app' => [
                'users.php' => file_get_contents(codecept_data_dir('users.php')),
            ],
        ])->url();

        $appDir = rtrim($rootDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;

        $this->userReader = new UserReader($appDir);
    }

    /**
     * @return void
     */
    public function testGetByIdentifier(): void
    {
        $identifier = 'foo';

        $user = $this->userReader->getByIdentifier($identifier);

        static::assertNotEquals(null, $user);
        static::assertEquals($identifier, $user->getIdentifier());
    }

    /**
     * @return void
     */
    public function testGetByIdentifierWithNonExistingUser(): void
    {
        $identifier = 'bar';

        $user = $this->userReader->getByIdentifier($identifier);

        static::assertEquals(null, $user);
    }
}
