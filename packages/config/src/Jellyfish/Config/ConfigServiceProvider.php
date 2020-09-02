<?php

declare(strict_types=1);

namespace Jellyfish\Config;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public const CONTAINER_KEY_CONFIG = 'config';

    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $self = $this;

        $pimple->offsetSet(static::CONTAINER_KEY_CONFIG, static function (Container $container) use ($self) {
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
