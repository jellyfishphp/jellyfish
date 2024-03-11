<?php

declare(strict_types=1);

namespace Jellyfish\Config;

use Codeception\Test\Unit;
use Exception;
use org\bovigo\vfs\vfsStream;

use function file_get_contents;
use function rtrim;

class ConfigTest extends Unit
{
    protected ConfigInterface $config;

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

        $appDir = rtrim($rootDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;
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
        $this->assertSame('default_config_value', $this->config->get('default_config_key'));
        $this->assertSame('1', $this->config->get('default_config_key_2'));
        $this->assertSame('0.2', $this->config->get('default_config_key_3'));
        $this->assertSame('1', $this->config->get('default_config_key_4'));
        $this->assertSame('testing_config_value', $this->config->get('testing_config_key'));
        $this->assertSame('eulav_gifnoc_derahs', $this->config->get('shared_config_key'));
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testGetWithUnsupportedValueType(): void
    {
        try {
            $this->config->get('default_config_key_5');
            self::fail();
        } catch (Exception) {
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testGetWithNotExistingEntry(): void
    {
        try {
            $this->assertSame('default_config_value', $this->config->get('staging_config_key'));
            self::fail();
        } catch (Exception) {
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testGetWithDefault(): void
    {
        $this->assertSame('staging_config_value', $this->config->get('staging_config_key', 'staging_config_value'));
    }
}
