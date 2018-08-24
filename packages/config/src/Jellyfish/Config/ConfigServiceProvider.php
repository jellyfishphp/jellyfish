<?php

namespace Jellyfish\Config;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple A container instance
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $pimple['config'] = function ($container) {
            $appDir = $container['appDir'];
            $environment = $container['environment'];

            return new Config($appDir, $environment);
        };
    }
}
