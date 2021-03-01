<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchema;

interface JsonSchemaFacadeInterface
{
    /**
     * @param string $schema
     * @param string $json
     *
     * @return bool
     */
    public function validate(string $schema, string $json): bool;
}
