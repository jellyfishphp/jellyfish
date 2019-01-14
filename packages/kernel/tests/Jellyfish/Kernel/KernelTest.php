<?php

namespace Jellyfish\Kernel;

use Codeception\Test\Unit;
use Jellyfish\Kernel\Exception\EnvVarNotSetException;
use org\bovigo\vfs\vfsStream;

class KernelTest extends Unit
{
    /**
     * @var string
     */
    protected $rootDir;

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
        \putenv('APPLICATION_ENV=development');

        $kernel = new Kernel($this->rootDir);

        $container = $kernel->getContainer();

        $this->assertTrue($container->offsetExists('key'));
        $this->assertEquals('value', $container->offsetGet('key'));

        $this->assertTrue($container->offsetExists('root_dir'));
        $this->assertEquals($this->rootDir . DIRECTORY_SEPARATOR, $container->offsetGet('root_dir'));

        $this->assertTrue($container->offsetExists('app_dir'));
        $this->assertEquals(
            $this->rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR,
            $container->offsetGet('app_dir')
        );

        $this->assertTrue($container->offsetExists('environment'));
        $this->assertEquals('development', $container->offsetGet('environment'));

        $this->assertTrue($container->offsetExists('commands'));
        $this->assertIsArray($container->offsetGet('commands'));
        $this->assertCount(0, $container->offsetGet('commands'));
    }

    /**
     * @return void
     *
     * @throws \Jellyfish\Kernel\Exception\EnvVarNotSetException
     */
    public function testGetContainerWithEmptyAppDir(): void
    {
        \putenv('APPLICATION_ENV=development');

        \unlink($this->rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'service_providers.php');

        $kernel = new Kernel($this->rootDir);

        $container = $kernel->getContainer();

        $this->assertFalse($container->offsetExists('key'));
    }

    /**
     * @return void
     */
    public function testInitKernelWithUnsetEnvVar(): void
    {
        \putenv('APPLICATION_ENV');

        try {
            new Kernel($this->rootDir);
            $this->fail();
        } catch (EnvVarNotSetException $e) {
        }
    }
}
