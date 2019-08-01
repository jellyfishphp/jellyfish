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
        $this->registerProcessFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\ProcessSymfony\ProcessSymfonyServiceProvider
     */
    protected function registerProcessFactory(Container $container): ProcessSymfonyServiceProvider
    {
        $container->offsetSet('process_factory', function () {
            return new ProcessFactory();
        });

        return $this;
    }
}
