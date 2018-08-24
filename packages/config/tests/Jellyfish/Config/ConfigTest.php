<?php

namespace Jellyfish\Config;

use Codeception\Test\Unit;
use Exception;
use org\bovigo\vfs\vfsStream;

class ConfigTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigInterface
     */
    protected $config;

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function _before(): void
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

        $this->config = new Config($appDir, $environment);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testGet(): void
    {
        $this->assertEquals('default_config_value', $this->config->get('default_config_key'));
        $this->assertEquals('testing_config_value', $this->config->get('testing_config_key'));
        $this->assertEquals('eulav_gifnoc_derahs', $this->config->get('shared_config_key'));
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testGetWithNotExistingEntry(): void
    {
        try {
            $this->assertEquals('default_config_value', $this->config->get('staging_config_key'));
            $this->fail();
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testGetWithDefault(): void
    {
        $this->assertEquals('staging_config_value',
            $this->config->get('staging_config_key', 'staging_config_value'));
    }

    /**
     * @return void
     */
    public function testHasKey(): void
    {
        $this->assertTrue($this->config->hasKey('testing_config_key'));
    }

    /**
     * @return void
     */
    public function testHasKeyWithNotExistingEntry(): void
    {
        $this->assertFalse($this->config->hasKey('staging_config_ke'));
    }

    /**
     * @return void
     */
    public function testHasValue(): void
    {
        $this->assertTrue($this->config->hasValue('testing_config_key'));
    }

    /**
     * @return void
     */
    public function testHasValueWithNotExistingEntry(): void
    {
        $this->assertFalse($this->config->hasValue(1));
    }
}
