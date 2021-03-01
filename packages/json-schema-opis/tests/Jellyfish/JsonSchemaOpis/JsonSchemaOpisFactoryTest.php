<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Codeception\Test\Unit;

class JsonSchemaOpisFactoryTest extends Unit
{
    /**
     * @var string
     */
    protected $schema;

    /**
     * @var \Jellyfish\JsonSchemaOpis\JsonSchemaOpisFactory
     */
    protected $jsonSchemaOpisFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->schema = '{}';

        $this->jsonSchemaOpisFactory = new JsonSchemaOpisFactory();
    }

    /**
     * @return void
     */
    public function testCreateValidator(): void
    {
        static::assertInstanceOf(
            Validator::class,
            $this->jsonSchemaOpisFactory->createValidator($this->schema)
        );
    }
}
