<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use Codeception\Test\Unit;
use Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProvider;

class SerializerSymfonyFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\SerializerSymfony\SerializerSymfonyFactory
     */
    protected $serializerSymfonyFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->serializerSymfonyFactory = new SerializerSymfonyFactory();
    }

    /**
     * @return void
     */
    public function testGetSerializer(): void
    {
        $serializer = $this->serializerSymfonyFactory->getSerializer();

        static::assertInstanceOf(Serializer::class, $serializer);
    }

    /**
     * @return void
     */
    public function testGetPropertyNameConverterStrategyProvider(): void
    {
        $propertyNameConverterStrategyProvider = $this->serializerSymfonyFactory
            ->getPropertyNameConverterStrategyProvider();

        static::assertInstanceOf(
            PropertyNameConverterStrategyProvider::class,
            $propertyNameConverterStrategyProvider
        );
    }
}
