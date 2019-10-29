<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Codeception\Test\Unit;

class ValidatorFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\JsonSchema\ValidatorFactoryInterface
     */
    protected $validatorFactory;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->schema = \file_get_contents(\codecept_data_dir('person.schema.json'));
        $this->validatorFactory = new ValidatorFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $this->assertInstanceOf(Validator::class, $this->validatorFactory->create($this->schema));
    }
}
