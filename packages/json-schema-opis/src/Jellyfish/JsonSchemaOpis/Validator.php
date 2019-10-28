<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Jellyfish\JsonSchema\ValidatorInterface;
use Opis\JsonSchema\ISchema as OpisSchemaInterface;
use Opis\JsonSchema\IValidator as OpisValidatorInterface;

class Validator implements ValidatorInterface
{
    /**
     * @var \Opis\JsonSchema\IValidator
     */
    protected $opisValidator;

    /**
     * @var \Opis\JsonSchema\ISchema
     */
    protected $opisSchema;

    /**
     * @param \Opis\JsonSchema\IValidator $opisValidator
     * @param \Opis\JsonSchema\ISchema $opisSchema
     */
    public function __construct(
        OpisValidatorInterface $opisValidator,
        OpisSchemaInterface $opisSchema
    ) {
        $this->opisValidator = $opisValidator;
        $this->opisSchema = $opisSchema;
    }

    /**
     * @param string $json
     *
     * @return bool
     */
    public function validate(string $json): bool
    {
        $result = $this->opisValidator->schemaValidation(\json_decode($json), $this->opisSchema);

        return $result->isValid();
    }
}
