<?php

declare(strict_types=1);

namespace Jellyfish\HttpLeague;

use Jellyfish\Http\ControllerInterface;
use Jellyfish\Http\HttpFacadeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class HttpLeagueFacade implements HttpFacadeInterface
{
    /**
     * @var \Jellyfish\HttpLeague\HttpLeagueFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\HttpLeague\HttpLeagueFactory $factory
     */
    public function __construct(HttpLeagueFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        return $this->factory->getRouter()->dispatch($request);
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function getCurrentRequest(): ServerRequestInterface
    {
        return $this->factory->getRequest();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    public function emit(ResponseInterface $response): bool
    {
        return $this->factory->getEmitter()->emit($response);
    }

    /**
     * @param string $method
     * @param string $path
     * @param \Jellyfish\Http\ControllerInterface $controller
     *
     * @return \Jellyfish\Http\HttpFacadeInterface
     */
    public function map(string $method, string $path, ControllerInterface $controller): HttpFacadeInterface
    {
        $this->factory->getRouter()->map($method, $path, $controller);

        return $this;
    }

    /**
     * @param \Psr\Http\Server\MiddlewareInterface $middleware
     *
     * @return \Jellyfish\Http\HttpFacadeInterface
     */
    public function addMiddleware(MiddlewareInterface $middleware): HttpFacadeInterface
    {
        $this->factory->getRouter()->middleware($middleware);

        return $this;
    }
}
