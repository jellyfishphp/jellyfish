<?php

declare(strict_types=1);

namespace Jellyfish\Config;

use ArrayObject;
use Exception;
use Jellyfish\Config\Exception\ConfigKeyNotFoundException;
use Jellyfish\Config\Exception\NotSupportedConfigValueTypeException;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;

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
     * @param string|null $default
     *
     * @return string
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    public function get(string $key, ?string $default = null): string
    {
        if ($default !== null && !$this->hasValue($key)) {
            return $default;
        }

        if (!$this->hasValue($key)) {
            throw new ConfigKeyNotFoundException(sprintf('Could not find key "%s" in "%s"', $key, __CLASS__));
        }

        return $this->getValue($key);
    }

    /**
     * @param string $key
     *
     * @return string
     *
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    protected function getValue(string $key): string
    {
        $value = $this->config[$key];

        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        throw new NotSupportedConfigValueTypeException(sprintf('Value type for key "%s" is not supported.', $key));
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    protected function hasValue(string $key): bool
    {
        return isset($this->config[$key]);
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
     * @param string|null $environment
     *
     * @return \ArrayObject
     */
    protected function buildConfig(ArrayObject $config, string $environment = null): ArrayObject
    {
        $configFile = $environment ?? self::CONFIG_FILE;
        $fileName = self::CONFIG_FILE_PREFIX . $configFile . self::CONFIG_FILE_SUFFIX;
        $pathToConfigFile = $this->appDir . $fileName;

        if (\file_exists($pathToConfigFile)) {
            include $pathToConfigFile;
        }

        return $config;
    }
}
