<?php

namespace Jellyfish\FinderSymfony;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FinderSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->createFinderFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createFinderFactory(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('finder_factory', function () {
            return new FinderFactory();
        });

        return $this;
    }
}
