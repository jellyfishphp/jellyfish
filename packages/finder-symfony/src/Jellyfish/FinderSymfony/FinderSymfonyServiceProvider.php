<?php

declare(strict_types=1);

namespace Jellyfish\FinderSymfony;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @see \Jellyfish\FinderSymfony\FinderSymfonyServiceProviderTest
 */
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
        $container->offsetSet('finder_factory', static fn(): FinderFactory => new FinderFactory());

        return $this;
    }
}
