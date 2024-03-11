<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Jellyfish\JsonSchema\ValidatorInterface;
use Opis\JsonSchema\Validator as OpisValidator;

class Validator implements ValidatorInterface
{
    /**
     * @var \Opis\JsonSchema\Validator
     */
    protected $opisValidator;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @param \Opis\JsonSchema\Validator $opisValidator
     * @param string $schema
     */
    public function __construct(
        OpisValidator $opisValidator,
        string $schema
    ) {
        $this->opisValidator = $opisValidator;
        $this->schema = $schema;
    }

    /**
     * @param string $json
     *
     * @return bool
     */
    public function validate(string $json): bool
    {
        return $this->opisValidator
            ->validate(\json_decode($json), $this->schema)
            ->isValid();
    }
}
