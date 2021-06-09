<?php

declare(strict_types=1);

namespace Jellyfish\HttpLeague;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use League\Route\Strategy\StrategyInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpLeagueFactory
{
    /**
     * @var \Psr\Http\Message\ServerRequestInterface|null
     */
    protected ?ServerRequestInterface $request = null;

    /**
     * @var \League\Route\Router|null
     */
    protected ?Router $router = null;

    /**
     * @var \Laminas\HttpHandlerRunner\Emitter\EmitterInterface|null
     */
    protected ?EmitterInterface $emitter = null;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        if ($this->request === null) {
            $this->request = ServerRequestFactory::fromGlobals();
        }

        return $this->request;
    }

    /**
     * @return \League\Route\Router
     */
    public function getRouter(): Router
    {
        if ($this->router === null) {
            $this->router = new Router();
            $this->router->setStrategy($this->createStrategy());
        }

        return $this->router;
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

    /**
     * @return \Laminas\HttpHandlerRunner\Emitter\EmitterInterface
     */
    public function getEmitter(): EmitterInterface
    {
        if ($this->emitter === null) {
            $this->emitter = new SapiStreamEmitter();
        }

        return $this->emitter;
    }
}
