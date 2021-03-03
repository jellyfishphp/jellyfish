<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use function json_decode;
use Opis\JsonSchema\ISchema as OpisSchemaInterface;
use Opis\JsonSchema\IValidator as OpisValidatorInterface;
use Opis\JsonSchema\Schema as OpisSchema;

use Opis\JsonSchema\Validator as OpisValidator;

class JsonSchemaOpisFactory
{
    /**
     * @param string $schema
     *
     * @return \Jellyfish\JsonSchemaOpis\ValidatorInterface
     */
    public function createValidator(string $schema): ValidatorInterface
    {
        return new Validator(
            $this->createOpisValidator(),
            $this->createOpisSchema($schema)
        );
    }

    /**
     * @param string $schema
     *
     * @return \Opis\JsonSchema\ISchema
     */
    protected function createOpisSchema(string $schema): OpisSchemaInterface
    {
        return new OpisSchema(json_decode($schema));
    }

    /**
     * @return \Opis\JsonSchema\IValidator
     */
    protected function createOpisValidator(): OpisValidatorInterface
    {
        return new OpisValidator();
    }
}
