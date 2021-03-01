<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Jellyfish\JsonSchema\JsonSchemaFacadeInterface;

class JsonSchemaOpisFacade implements JsonSchemaFacadeInterface
{
    /**
     * @var \Jellyfish\JsonSchemaOpis\JsonSchemaOpisFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\JsonSchemaOpis\JsonSchemaOpisFactory $factory
     */
    public function __construct(JsonSchemaOpisFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $schema
     * @param string $json
     *
     * @return bool
     */
    public function validate(string $schema, string $json): bool
    {
        return $this->factory->createValidator($schema)->validate($json);
    }
}
