<?php

declare(strict_types=1);

namespace Jellyfish\HttpLeague;

use Http\Factory\Diactoros\ResponseFactory;
use Jellyfish\Http\HttpConstants;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use League\Route\Strategy\StrategyInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

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
     * @return \Pimple\Container
     */
    protected function registerHttpFacade(Container $container): Container
    {
        $container->offsetSet(HttpConstants::FACADE, static function () {
            $httpLeagueFactory = new HttpLeagueFactory();

            return new HttpLeagueFacade($httpLeagueFactory);
        });

        return $container;
    }
}
