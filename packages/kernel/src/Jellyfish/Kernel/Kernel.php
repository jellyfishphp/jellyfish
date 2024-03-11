<?php

declare(strict_types = 1);

namespace Jellyfish\Kernel;

use ArrayObject;
use Jellyfish\Kernel\Exception\EnvVarNotSetException;
use Pimple\Container;

/**
 * @see \Jellyfish\Kernel\KernelTest
 */
class Kernel implements KernelInterface
{
    protected const SERVICE_PROVIDERS_FILE_NAME = 'service_providers.php';

    protected const APP_DIRECTORY_NAME = 'app';

    protected Container $container;

    protected string $rootDir;

    protected string $appDir;

    protected string $environment;

    /**
     * @param string $rootDir
     *
     * @throws \Jellyfish\Kernel\Exception\EnvVarNotSetException
     */
    public function __construct(string $rootDir)
    {
        $this->rootDir = \rtrim($rootDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->appDir = $this->rootDir . static::APP_DIRECTORY_NAME . DIRECTORY_SEPARATOR;
        $this->environment = $this->buildEnvironment();
        $this->container = $this->buildContainer();
    }

    /**
     * @return string
     *
     * @throws \Jellyfish\Kernel\Exception\EnvVarNotSetException
     */
    protected function buildEnvironment(): string
    {
        $environment = \getenv('APPLICATION_ENV', true) ?: \getenv('APPLICATION_ENV');

        if (!$environment) {
            throw new EnvVarNotSetException('Environment variable "APPLICATION_ENV" is not set.');
        }

        return $environment;
    }

    /**
     * @return \Pimple\Container
     */
    protected function buildContainer(): Container
    {
        $container = new Container([
            'root_dir' => $this->rootDir,
            'app_dir' => $this->appDir,
            'environment' => $this->environment,
            'commands' => static fn (): array => [],
        ]);

        $serviceProviders = $this->buildServiceProviders();

        foreach ($serviceProviders as $serviceProvider) {
            $container->register($serviceProvider);
        }

        return $container;
    }

    /**
     * @return \ArrayObject
     */
    protected function buildServiceProviders(): ArrayObject
    {
        $serviceProviders = new ArrayObject();
        $pathToServiceProvidersFile = $this->appDir . static::SERVICE_PROVIDERS_FILE_NAME;

        if (\file_exists($pathToServiceProvidersFile)) {
            include $pathToServiceProvidersFile;
        }

        return $serviceProviders;
    }

    /**
     * @return \Pimple\Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
