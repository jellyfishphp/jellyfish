<?php

declare(strict_types=1);

namespace Jellyfish\Uuid;

interface UuidFacadeInterface
{
    /**
     * @return string
     */
    public function generateUuid(): string;
}
