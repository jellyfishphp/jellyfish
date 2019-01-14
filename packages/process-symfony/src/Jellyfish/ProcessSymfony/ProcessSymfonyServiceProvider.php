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
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createProcessFactory(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('process_factory', function () {
            return new ProcessFactory();
        });

        return $this;
    }
}
