<?php

use Jellyfish\HttpAuthentication\User;

$users['foo'] = (new User())->setIdentifier('foo')
    ->setPassword(password_hash('bar', PASSWORD_BCRYPT))
    ->setPathRegEx('/\/.*/');
