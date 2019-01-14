<?php

namespace Jellyfish\JsonSchema;

interface ValidatorInterface
{
    /**
     * @param string $json
     *
     * @return bool
     */
    public function validate(string $json): bool;
}
