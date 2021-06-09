<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Opis\JsonSchema\ISchema as OpisSchemaInterface;
use Opis\JsonSchema\IValidator as OpisValidatorInterface;

use function json_decode;

class Validator implements ValidatorInterface
{
    /**
     * @var \Opis\JsonSchema\IValidator
     */
    protected OpisValidatorInterface $opisValidator;

    /**
     * @var \Opis\JsonSchema\ISchema
     */
    protected OpisSchemaInterface $opisSchema;

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
        $result = $this->opisValidator->schemaValidation(json_decode($json), $this->opisSchema);

        return $result->isValid();
    }
}
