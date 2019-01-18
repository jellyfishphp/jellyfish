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
        $self = $this;

        $pimple->offsetSet('request', function () use ($self) {
            return $self->createServerRequest();
        });

        $pimple->offsetSet('router', function () use ($self) {
            return $self->createRouter();
        });

        $pimple->offsetSet('emitter', function () use ($self) {
            return $self->createEmitter();
        });
    }

    /**
     * @SuppressWarnings(PHPMD)
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createServerRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
    }

    /**
     * @return \Psr\Http\Message\ResponseFactoryInterface
     */
    protected function createResponseFactory(): ResponseFactoryInterface
    {
        return new ResponseFactory();
    }

    /**
     * @return \League\Route\Strategy\StrategyInterface
     */
    protected function createStrategy(): StrategyInterface
    {
        return new JsonStrategy($this->createResponseFactory());
    }

    /**
     * @return \League\Route\Router
     */
    protected function createRouter(): Router
    {
        $router = new Router();

        $router->setStrategy($this->createStrategy());

        return $router;
    }

    /**
     * @return \Zend\HttpHandlerRunner\Emitter\EmitterInterface
     */
    protected function createEmitter(): EmitterInterface
    {
        return new SapiStreamEmitter();
    }
}
