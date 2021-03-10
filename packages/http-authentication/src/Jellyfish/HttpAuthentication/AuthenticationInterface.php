<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return bool
     */
    public function authenticate(ServerRequestInterface $request): bool;
}
