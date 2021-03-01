<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Codeception\Test\Unit;
use Opis\JsonSchema\ISchema as OpisSchemaInterface;
use Opis\JsonSchema\IValidator as OpisValidatorInterface;
use Opis\JsonSchema\ValidationResult as OpisValidationResult;

use function json_decode;

class ValidatorTest extends Unit
{
    /**
     * @var \Opis\JsonSchema\ISchema|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $opisSchemaMock;

    /**
     * @var \Opis\JsonSchema\IValidator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $opisValidatorMock;

    /**
     * @var \Opis\JsonSchema\ValidationResult|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $opisValidationResult;

    /**
     * @var \Jellyfish\JsonSchemaOpis\ValidatorInterface
     */
    protected $validator;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->opisSchemaMock = $this->getMockBuilder(OpisSchemaInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->opisValidatorMock = $this->getMockBuilder(OpisValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->opisValidationResult = new OpisValidationResult();

        $this->validator = new Validator($this->opisValidatorMock, $this->opisSchemaMock);
    }

    /**
     * @return void
     */
    public function testValidate(): void
    {
        $json = '{"name", "test"}';

        $this->opisValidatorMock->expects(static::atLeastOnce())
            ->method('schemaValidation')
            ->with(json_decode($json), $this->opisSchemaMock)
            ->willReturn($this->opisValidationResult);

        static::assertTrue($this->validator->validate($json));
    }
}
