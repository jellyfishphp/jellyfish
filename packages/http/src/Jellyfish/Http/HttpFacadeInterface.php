<?php

declare(strict_types=1);

namespace Jellyfish\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

interface HttpFacadeInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface;

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function getCurrentRequest(): ServerRequestInterface;

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    public function emit(ResponseInterface $response): bool;

    /**
     * @param string $method
     * @param string $path
     * @param \Jellyfish\Http\ControllerInterface $controller
     *
     * @return \Jellyfish\Http\HttpFacadeInterface
     */
    public function map(string $method, string $path, ControllerInterface $controller): HttpFacadeInterface;

    /**
     * @param \Psr\Http\Server\MiddlewareInterface $middleware
     *
     * @return \Jellyfish\Http\HttpFacadeInterface
     */
    public function addMiddleware(MiddlewareInterface $middleware): HttpFacadeInterface;
}
