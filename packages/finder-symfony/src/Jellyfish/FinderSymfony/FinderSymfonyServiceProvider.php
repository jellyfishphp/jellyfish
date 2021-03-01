<?php

declare(strict_types=1);

namespace Jellyfish\FinderSymfony;

use Jellyfish\Finder\FinderConstants;
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
        $this->registerFinderFacade($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\FinderSymfony\FinderSymfonyServiceProvider
     */
    protected function registerFinderFacade(Container $container): FinderSymfonyServiceProvider
    {
        $container->offsetSet(FinderConstants::FACADE, function () {
            return new FinderSymfonyFacade(new FinderSymfonyFactory());
        });

        return $this;
    }
}
