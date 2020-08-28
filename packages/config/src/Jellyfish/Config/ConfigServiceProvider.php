<?php

declare(strict_types=1);

namespace Jellyfish\Config;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $self = $this;

        $pimple->offsetSet('config', static function (Container $container) use ($self) {
            return $self->createConfig($container);
        });
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Config\ConfigInterface
     *
     * @throws \Exception
     */
    protected function createConfig(Container $container): ConfigInterface
    {
        $appDir = $container->offsetGet('app_dir');
        $environment = $container->offsetGet('environment');

        return new Config($appDir, $environment);
    }
}
