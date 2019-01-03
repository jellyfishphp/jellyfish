<?php

namespace Jellyfish\Config;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;
use Pimple\Container;

class ConfigServiceProviderTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigServiceProvider
     */
    protected $configServiceProvider;

    /**
     * @var \Pimple\Container|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $container;

    /**
     * @return void
     */
    protected function _before()
    {
        parent::_before();

        $rootDir = vfsStream::setup('root', null, [
            'app' => [
                'config-default.php' => file_get_contents(codecept_data_dir('config-default.php')),
                'config-testing.php' => file_get_contents(codecept_data_dir('config-testing.php')),
            ],
        ])->url();

        $appDir = rtrim($rootDir,DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $environment = 'testing';

        $this->configServiceProvider = new ConfigServiceProvider();
        $this->container = new Container([
            'app_dir' => $appDir,
            'environment' => $environment
        ]);
    }

    /**
     * @return void
     */
    public function testRegister()
    {
        $this->configServiceProvider->register($this->container);
        $this->assertInstanceOf(Config::class, $this->container['config']);
    }
}
