<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;

use function file_get_contents;

class HttpAuthenticationFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @var \Jellyfish\HttpAuthentication\HttpAuthenticationFactory
     */
    protected $httpAuthenticationFactory;

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

        $this->httpAuthenticationFactory = new HttpAuthenticationFactory($appDir);
    }

    /**
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     *
     * @return void
     */
    public function testGetAuthentication(): void
    {
        static::assertInstanceOf(
            BasicAuthentication::class,
            $this->httpAuthenticationFactory->getAuthentication()
        );
    }
}
