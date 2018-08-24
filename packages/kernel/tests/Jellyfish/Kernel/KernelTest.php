<?php

namespace Jellyfish\Kernel;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;

class KernelTest extends Unit
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @return void
     *
     * @throws Exception\EnvVarNotFoundException
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
     */
    public function testGetContainer(): void
    {
        $kernel = new Kernel($this->rootDir);

        $container = $kernel->getContainer();

        $this->assertTrue($container->offsetExists('key'));
        $this->assertEquals('value', $container->offsetGet('key'));
    }

    /**
     * @return void
     */
    public function testGetContainerWithEmptyAppDir(): void
    {
        unlink($this->rootDir . DIRECTORY_SEPARATOR .'app' . DIRECTORY_SEPARATOR . 'service_providers.php');

        $kernel = new Kernel($this->rootDir);

        $container = $kernel->getContainer();

        $this->assertFalse($container->offsetExists('key'));
    }
}
