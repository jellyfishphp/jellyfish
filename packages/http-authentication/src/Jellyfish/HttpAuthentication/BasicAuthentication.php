<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Psr\Http\Message\ServerRequestInterface;

use function base64_decode;
use function count;
use function explode;
use function is_array;
use function str_replace;

class BasicAuthentication implements AuthenticationInterface
{
    protected const TYPE = 'Basic';

    /**
     * @var \Jellyfish\HttpAuthentication\UserInterface
     */
    protected $user;

    /**
     * @param \Jellyfish\HttpAuthentication\UserInterface $user
     */
    public function __construct(
        UserInterface $user
    ) {
        $this->user = $user;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return bool
     */
    public function authenticate(ServerRequestInterface $request): bool
    {
        if (!$request->hasHeader('Authorization')) {
            return false;
        }

        $authorizationHeader = $request->getHeader('Authorization');

        if (!is_array($authorizationHeader) || count($authorizationHeader) !== 1) {
            return false;
        }

        return $this->validateType($authorizationHeader[0]) && $this->validateCredentials($authorizationHeader[0]);
    }

    /**
     * @param string $authorizationHeader
     *
     * @return bool
     */
    protected function validateType(string $authorizationHeader): bool
    {
        $authorizationHeaderParts = explode(' ', $authorizationHeader);

        return count($authorizationHeaderParts) === 2 && $authorizationHeaderParts[0] === static::TYPE;
    }

    /**
     * @param string $authorizationHeader
     *
     * @return bool
     */
    protected function validateCredentials(string $authorizationHeader): bool
    {
        $decodedCredentials = base64_decode(str_replace('Basic ', '', $authorizationHeader));
        $credentials = explode(':', $decodedCredentials);

        return count($credentials) === 2
            && $this->user->getIdentifier() === $credentials[0]
            && $this->user->getPassword() === $credentials[1];
    }
}
