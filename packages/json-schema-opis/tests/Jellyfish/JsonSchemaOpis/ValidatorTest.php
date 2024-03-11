<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Codeception\Test\Unit;
use Opis\JsonSchema\Validator as OpisValidator;
use Opis\JsonSchema\ValidationResult as OpisValidationResult;
use PHPUnit\Framework\MockObject\MockObject;

class ValidatorTest extends Unit
{
    protected string $schema;

    protected MockObject&OpisValidator $opisValidatorMock;

    protected MockObject&OpisValidationResult $opisValidationResultMock;

    protected Validator $validator;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->schema = '{}';

        $this->opisValidatorMock = $this->getMockBuilder(OpisValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->opisValidationResultMock = $this->getMockBuilder(OpisValidationResult::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator = new Validator($this->opisValidatorMock, $this->schema);
    }

    /**
     * @return void
     */
    public function testValidate(): void
    {
        $json = '{"name", "test"}';

        $this->opisValidatorMock->expects($this->atLeastOnce())
            ->method('validate')
            ->with(\json_decode($json), $this->schema)
            ->willReturn($this->opisValidationResultMock);

        $this->opisValidationResultMock->expects($this->atLeastOnce())
            ->method('isValid')
            ->willReturn(true);

        $this->assertTrue($this->validator->validate($json));
    }
}
