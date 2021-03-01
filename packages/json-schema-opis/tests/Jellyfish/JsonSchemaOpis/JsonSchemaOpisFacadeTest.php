<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Codeception\Test\Unit;

class JsonSchemaOpisFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\JsonSchemaOpis\JsonSchemaOpisFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jsonSchemaOpisFactoryMock;

    /**
     * @var \Jellyfish\JsonSchemaOpis\ValidatorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $validatorMock;

    /**
     * @var string
     */
    protected $json;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @var \Jellyfish\JsonSchemaOpis\JsonSchemaOpisFacade
     */
    protected $jsonSchemaOpisFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->jsonSchemaOpisFactoryMock = $this->getMockBuilder(JsonSchemaOpisFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->validatorMock = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->json = '{}';
        $this->schema = '{}';

        $this->jsonSchemaOpisFacade = new JsonSchemaOpisFacade(
            $this->jsonSchemaOpisFactoryMock
        );
    }

    /**
     * @return void
     */
    public function testValidate(): void
    {
        $this->jsonSchemaOpisFactoryMock->expects(static::atLeastOnce())
            ->method('createValidator')
            ->with($this->schema)
            ->willReturn($this->validatorMock);

        $this->validatorMock->expects(static::atLeastOnce())
            ->method('validate')
            ->with($this->json)
            ->willReturn(true);

        static::assertTrue($this->jsonSchemaOpisFacade->validate($this->schema, $this->json));
    }
}
