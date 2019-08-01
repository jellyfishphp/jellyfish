<?php

namespace Jellyfish\Http;

use Http\Factory\Diactoros\ResponseFactory;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use League\Route\Strategy\StrategyInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

class HttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerRequest($pimple)
            ->registerRouter($pimple)
            ->registerEmitter($pimple);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Http\HttpServiceProvider
     */
    protected function registerRequest(Container $container): HttpServiceProvider
    {
        $container->offsetSet('request', function () {
            return ServerRequestFactory::fromGlobals();
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Http\HttpServiceProvider
     */
    protected function registerRouter(Container $container): HttpServiceProvider
    {
        $self = $this;

        $container->offsetSet('router', function () use ($self) {
            $router = new Router();

            $router->setStrategy($self->createStrategy());

            return $router;
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Http\HttpServiceProvider
     */
    protected function registerEmitter(Container $container): HttpServiceProvider
    {
        $container->offsetSet('emitter', function () {
            return new SapiStreamEmitter();
        });

        return $this;
    }

    /**
     * @return \League\Route\Strategy\StrategyInterface
     */
    protected function createStrategy(): StrategyInterface
    {
        return new JsonStrategy($this->createResponseFactory());
    }

    /**
     * @return \Psr\Http\Message\ResponseFactoryInterface
     */
    protected function createResponseFactory(): ResponseFactoryInterface
    {
        return new ResponseFactory();
    }
}
