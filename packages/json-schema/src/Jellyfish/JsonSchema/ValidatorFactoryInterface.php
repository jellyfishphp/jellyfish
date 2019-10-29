<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchema;

interface ValidatorFactoryInterface
{
    /**
     * @param string $schema
     *
     * @return \Jellyfish\JsonSchema\ValidatorInterface
     */
    public function create(string $schema): ValidatorInterface;
}
