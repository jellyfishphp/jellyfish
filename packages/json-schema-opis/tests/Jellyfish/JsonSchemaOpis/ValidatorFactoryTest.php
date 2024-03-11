<?php

declare(strict_types = 1);

namespace Jellyfish\JsonSchemaOpis;

use Codeception\Test\Unit;
use Exception;

class ValidatorFactoryTest extends Unit
{
    protected string $schema;

    protected ValidatorFactory $validatorFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $schema = \file_get_contents(\codecept_data_dir('person.schema.json'));

        if ($schema === false) {
            throw new Exception('Person schema not found.');
        }

        $this->schema = $schema;
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
