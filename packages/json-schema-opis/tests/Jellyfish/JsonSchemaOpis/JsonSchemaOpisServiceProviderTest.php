<?php

declare(strict_types=1);

namespace Jellyfish\JsonSchemaOpis;

use Codeception\Test\Unit;
use Jellyfish\JsonSchema\JsonSchemaConstants;
use Jellyfish\JsonSchema\JsonSchemaFacadeInterface;
use Pimple\Container;

class JsonSchemaOpisServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\ServiceProviderInterface
     */
    protected $jsonSchemaOpisServiceProvider;

    /**
     * @var \Pimple\Container
     */
    protected $container;

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

        static::assertTrue($this->container->offsetExists(JsonSchemaConstants::FACADE));
        static::assertInstanceOf(
            JsonSchemaFacadeInterface::class,
            $this->container->offsetGet(JsonSchemaConstants::FACADE)
        );
    }
}
