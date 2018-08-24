<?php

namespace Jellyfish\Config;

use ArrayObject;
use Exception;
use Jellyfish\Config\Exception\ConfigKeyNotFoundException;
use Jellyfish\Config\Exception\ConstantNotDefinedException;

class Config implements ConfigInterface
{
    protected const CONFIG_FILE_PREFIX = 'config-';
    protected const CONFIG_FILE = 'default';
    protected const CONFIG_FILE_SUFFIX = '.php';

    /**
     * @var \ArrayObject|null
     */
    protected $config;

    /**
     * @var string
     */
    protected $appDir;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @param string $appDir
     * @param string $environment
     *
     * @throws Exception
     */
    public function __construct(
        string $appDir,
        string $environment
    ) {
        $this->appDir = $appDir;
        $this->environment = $environment;

        $this->initialize();
    }

    /**
     * @param string $key
     * @param $default
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function get(string $key, $default = null)
    {
        if ($default !== null && !$this->hasValue($key)) {
            return $default;
        }

        if (!$this->hasValue($key)) {
            throw new ConfigKeyNotFoundException(sprintf('Could not find config key "%s" in "%s"', $key, __CLASS__));
        }

        return $this->config[$key];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasValue(string $key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function initialize(): void
    {
        $config = new ArrayObject();

        $this->buildConfig($config);
        $this->buildConfig($config, $this->environment);

        $this->config = $config;
    }

    /**
     * @param \ArrayObject $config
     * @param string $environment
     *
     * @return \ArrayObject
     */
    protected function buildConfig(ArrayObject $config, string $environment = null): ArrayObject
    {
        $configFile = $environment ?? self::CONFIG_FILE;
        $fileName = self::CONFIG_FILE_PREFIX . $configFile . self::CONFIG_FILE_SUFFIX;
        $pathToConfigFile = $this->appDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($pathToConfigFile)) {
            include $pathToConfigFile;
        }

        return $config;
    }
}
