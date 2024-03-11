<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Codeception\Test\Unit;
use Pimple\Container;

class JsonSchemaOpisServiceProviderTest extends Unit
{
    protected Container $container;

    protected JsonSchemaOpisServiceProvider $jsonSchemaOpisServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->jsonSchemaOpisServiceProvider = new JsonSchemaOpisServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->jsonSchemaOpisServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists('json_schema_validator_factory'));
        $this->assertInstanceOf(
            ValidatorFactory::class,
            $this->container->offsetGet('json_schema_validator_factory')
        );
    }
}
