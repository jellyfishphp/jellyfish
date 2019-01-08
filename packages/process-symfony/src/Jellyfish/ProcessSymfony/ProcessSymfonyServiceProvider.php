<?php

namespace Jellyfish\ProcessSymfony;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ProcessSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->createProcessFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\ProcessSymfony\ProcessSymfonyServiceProvider
     */
    protected function createProcessFactory(Container $container): ProcessSymfonyServiceProvider
    {
        $container->offsetSet('process_factory', function (Container $container) {
            $rootDir = $container->offsetGet('root_dir');

            return new ProcessFactory($rootDir . 'tmp' . DIRECTORY_SEPARATOR);
        });

        return $this;
    }
}
