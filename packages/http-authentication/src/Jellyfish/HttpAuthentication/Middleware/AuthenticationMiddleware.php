<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication\Middleware;

use Jellyfish\HttpAuthentication\HttpAuthenticationFacadeInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var \Jellyfish\HttpAuthentication\HttpAuthenticationFacadeInterface
     */
    protected HttpAuthenticationFacadeInterface $httpAuthenticationFacade;

    /**
     * @param \Jellyfish\HttpAuthentication\HttpAuthenticationFacadeInterface $httpAuthenticationFacade
     */
    public function __construct(HttpAuthenticationFacadeInterface $httpAuthenticationFacade)
    {
        $this->httpAuthenticationFacade = $httpAuthenticationFacade;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->httpAuthenticationFacade->authenticate($request)) {
            return $handler->handle($request);
        }

        return new EmptyResponse(401);
    }
}
