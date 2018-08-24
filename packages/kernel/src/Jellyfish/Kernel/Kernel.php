<?php

namespace Jellyfish\Kernel;

use ArrayObject;
use Pimple\Container;

class Kernel implements KernelInterface
{
    protected const SERVICE_PROVIDERS_FILE_NAME = 'service_providers.php';
    protected const APP_DIRECTORY_NAME = 'app';

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $appDir;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @param string $rootDir
     * @param string $environment
     */
    public function __construct(string $rootDir, string $environment = 'development')
    {
        $this->rootDir = rtrim($rootDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->appDir = $this->rootDir . static::APP_DIRECTORY_NAME . DIRECTORY_SEPARATOR;
        $this->environment = $environment;
        $this->buildContainer();
    }

    /**
     * @return void
     */
    protected function buildContainer(): void
    {
        $this->container = new Container();

        $serviceProviders = $this->buildServiceProviders();

        foreach ($serviceProviders as $serviceProvider) {
            $this->container->register($serviceProvider);
        }
    }

    /**
     * @return ArrayObject
     */
    protected function buildServiceProviders(): ArrayObject
    {
        $serviceProviders = new ArrayObject();
        $pathToServiceProvidersFile = $this->appDir . static::SERVICE_PROVIDERS_FILE_NAME;

        if (file_exists($pathToServiceProvidersFile)) {
            include $pathToServiceProvidersFile;
        }

        return $serviceProviders;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
