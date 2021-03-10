<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Psr\Http\Message\ServerRequestInterface;

interface HttpAuthenticationFacadeInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $serverRequest
     *
     * @return bool
     */
    public function authenticate(ServerRequestInterface $serverRequest): bool;
}
