<?php

declare(strict_types=1);

namespace Jellyfish\Kernel;

use Codeception\Test\Unit;
use Jellyfish\Kernel\Exception\EnvVarNotSetException;
use org\bovigo\vfs\vfsStream;

use function file_get_contents;
use function putenv;
use function unlink;

class KernelTest extends Unit
{
    /**
     * @var string
     */
    protected string $rootDir;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->rootDir = vfsStream::setup('root', null, [
            'app' => [
                'service_providers.php' => file_get_contents(codecept_data_dir('service_providers.php')),
            ],
        ])->url();
    }

    /**
     * @return void
     *
     * @throws \Jellyfish\Kernel\Exception\EnvVarNotSetException
     */
    public function testGetContainer(): void
    {
        putenv('APPLICATION_ENV=development');

        $kernel = new Kernel($this->rootDir);

        $container = $kernel->getContainer();

        static::assertTrue($container->offsetExists('key'));
        static::assertEquals('value', $container->offsetGet('key'));

        static::assertTrue($container->offsetExists('root_dir'));
        static::assertEquals($this->rootDir . DIRECTORY_SEPARATOR, $container->offsetGet('root_dir'));

        static::assertTrue($container->offsetExists('app_dir'));
        static::assertEquals(
            $this->rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR,
            $container->offsetGet('app_dir')
        );

        static::assertTrue($container->offsetExists('environment'));
        static::assertEquals('development', $container->offsetGet('environment'));
    }

    /**
     * @return void
     *
     * @throws \Jellyfish\Kernel\Exception\EnvVarNotSetException
     */
    public function testGetContainerWithEmptyAppDir(): void
    {
        putenv('APPLICATION_ENV=development');

        unlink($this->rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'service_providers.php');

        $kernel = new Kernel($this->rootDir);

        $container = $kernel->getContainer();

        static::assertFalse($container->offsetExists('key'));
    }

    /**
     * @return void
     */
    public function testInitKernelWithUnsetEnvVar(): void
    {
        putenv('APPLICATION_ENV');

        try {
            new Kernel($this->rootDir);
            static::fail();
        } catch (EnvVarNotSetException $e) {
        }
    }
}
