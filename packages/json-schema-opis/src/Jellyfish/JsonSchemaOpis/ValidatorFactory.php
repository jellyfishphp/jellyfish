<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Jellyfish\JsonSchema\ValidatorFactoryInterface;
use Jellyfish\JsonSchema\ValidatorInterface;
use Opis\JsonSchema\Schema as OpisSchema;
use Opis\JsonSchema\Validator as OpisValidator;

class ValidatorFactory implements ValidatorFactoryInterface
{
    /**
     * @param string $schema
     *
     * @return \Jellyfish\JsonSchema\ValidatorInterface
     */
    public function create(string $schema): ValidatorInterface
    {
        $schema = new OpisSchema(\json_decode($schema));
        $validator = new OpisValidator();

        return new Validator($validator, $schema);
    }
}
