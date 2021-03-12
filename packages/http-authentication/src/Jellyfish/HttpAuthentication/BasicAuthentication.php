<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Psr\Http\Message\ServerRequestInterface;

use function base64_decode;
use function count;
use function explode;
use function is_array;
use function password_verify;
use function preg_match;
use function str_replace;

class BasicAuthentication implements AuthenticationInterface
{
    protected const TYPE = 'Basic';

    /**
     * @var \Jellyfish\HttpAuthentication\UserReaderInterface
     */
    protected $userReader;

    /**
     * @param \Jellyfish\HttpAuthentication\UserReaderInterface $userReader
     */
    public function __construct(
        UserReaderInterface $userReader
    ) {
        $this->userReader = $userReader;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return bool
     */
    public function authenticate(ServerRequestInterface $request): bool
    {
        $authorizationHeader = $this->getAuthorizationHeaderByServerRequest($request);
        $path = $request->getUri()->getPath();

        return $authorizationHeader !== null
            && $this->validateType($authorizationHeader)
            && $this->validateCredentials($authorizationHeader, $path);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return string|null
     */
    protected function getAuthorizationHeaderByServerRequest(ServerRequestInterface $request): ?string
    {
        if (!$request->hasHeader('Authorization')) {
            return null;
        }

        $authorizationHeader = $request->getHeader('Authorization');

        if (!is_array($authorizationHeader) || count($authorizationHeader) !== 1) {
            return null;
        }

        return $authorizationHeader[0];
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
     * @param string $path
     *
     * @return bool
     */
    protected function validateCredentials(string $authorizationHeader, string $path): bool
    {
        $decodedCredentials = base64_decode(str_replace('Basic ', '', $authorizationHeader));
        $credentials = explode(':', $decodedCredentials);

        if (count($credentials) !== 2) {
            return false;
        }

        $user = $this->userReader->getByIdentifier($credentials[0]);

        return $user !== null
            && $user->getIdentifier() === $credentials[0]
            && password_verify($credentials[1], $user->getPassword())
            && preg_match($user->getPathRegEx(), $path);
    }
}
