<?php

declare(strict_types=1);

namespace Jellyfish\HttpLeague;

use Jellyfish\Http\HttpConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HttpLeagueServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerHttpFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\HttpLeague\HttpLeagueServiceProvider
     */
    protected function registerHttpFacade(Container $container): HttpLeagueServiceProvider
    {
        $container->offsetSet(HttpConstants::FACADE, static function () {
            $httpLeagueFactory = new HttpLeagueFactory();

            return new HttpLeagueFacade($httpLeagueFactory);
        });

        return $this;
    }
}
