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
        $this->registerFinderFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\FinderSymfony\FinderSymfonyServiceProvider
     */
    protected function registerFinderFactory(Container $container): FinderSymfonyServiceProvider
    {
        $container->offsetSet('finder_factory', function () {
            return new FinderFactory();
        });

        return $this;
    }
}
