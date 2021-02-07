<?php

declare(strict_types=1);

namespace Jellyfish\Config;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerConfigFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Config\ConfigServiceProvider
     */
    public function registerConfigFacade(Container $container): ConfigServiceProvider
    {
        $container->offsetSet(ConfigConstants::FACADE, static function (Container $container) {
            $appDir = $container->offsetGet('app_dir');
            $environment = $container->offsetGet('environment');

            return new ConfigFacade(
                new ConfigFactory($appDir, $environment)
            );
        });

        return $this;
    }
}
