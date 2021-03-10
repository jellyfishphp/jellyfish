<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Psr\Http\Message\ServerRequestInterface;

class HttpAuthenticationFacade implements HttpAuthenticationFacadeInterface
{
    /**
     * @var \Jellyfish\HttpAuthentication\HttpAuthenticationFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\HttpAuthentication\HttpAuthenticationFactory $factory
     */
    public function __construct(HttpAuthenticationFactory $factory)
    {
        $this->factory = $factory;
    }


    /**
     * @param \Psr\Http\Message\ServerRequestInterface $serverRequest
     *
     * @return bool
     */
    public function authenticate(ServerRequestInterface $serverRequest): bool
    {
        return $this->factory->getAuthentication()
            ->authenticate($serverRequest);
    }
}
