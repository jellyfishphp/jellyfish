<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

interface HttpAuthenticationConstants
{
    public const FACADE = 'facade_http_authentication';

    public const USER_IDENTIFIER = 'USER_IDENTIFIER';
    public const USER_PASSWORD = 'USER_PASSWORD';

    public const DEFAULT_USER_IDENTIFIER = 'jellyfish';
    public const DEFAULT_USER_PASSWORD = 'hsifyllej';
}
