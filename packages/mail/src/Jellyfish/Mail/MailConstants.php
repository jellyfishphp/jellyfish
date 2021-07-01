<?php

declare(strict_types=1);

namespace Jellyfish\Mail;

interface MailConstants
{
    public const FACADE = 'facade_mail';

    public const SMTP_HOST = 'SMTP_HOST';
    public const SMTP_PORT = 'SMTP_PORT';
    public const SMTP_ENCRYPTION = 'SMTP_ENCRYPTION';
    public const SMTP_AUTH_MODE = 'SMTP_AUTH_MODE';
    public const SMTP_USERNAME = 'SMTP_USERNAME';
    public const SMTP_PASSWORD = 'SMTP_PASSWORD';

    public const DEFAULT_SMTP_HOST = 'localhost';
    public const DEFAULT_SMTP_PORT = '25';
    public const DEFAULT_SMTP_ENCRYPTION = '';
    public const DEFAULT_SMTP_AUTH_MODE = '';
    public const DEFAULT_SMTP_USERNAME = '';
    public const DEFAULT_SMTP_PASSWORD = '';
}
