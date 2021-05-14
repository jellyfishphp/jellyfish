<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony\NameConverter;

use Codeception\Test\Unit;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface;

class PropertyNameConverterStrategyProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface
     */
    protected $propertyNameConverterStrategyMock;

    /**
     * @var string
     */
    protected string $propertyNameConverterStrategyKey;

    /**
     * @var \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProvider
     */
    protected PropertyNameConverterStrategyProvider $propertyNameConverterStrategyProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->propertyNameConverterStrategyMock = $this->getMockBuilder(PropertyNameConverterStrategyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyNameConverterStrategyKey = 'test';

        $this->propertyNameConverterStrategyProvider = new PropertyNameConverterStrategyProvider();
    }

    /**
     * @return void
     */
    public function testAddAndRemoveStrategy(): void
    {
        static::assertEquals(
            $this->propertyNameConverterStrategyProvider,
            $this->propertyNameConverterStrategyProvider->add(
                $this->propertyNameConverterStrategyKey,
                $this->propertyNameConverterStrategyMock
            )
        );

        static::assertEquals(
            $this->propertyNameConverterStrategyProvider,
            $this->propertyNameConverterStrategyProvider->remove(
                $this->propertyNameConverterStrategyKey
            )
        );

        $hasStrategy = $this->propertyNameConverterStrategyProvider->has(
            $this->propertyNameConverterStrategyKey
        );

        static::assertFalse($hasStrategy);
    }

    /**
     * @return void
     */
    public function testHasStrategy(): void
    {
        $hasStrategy = $this->propertyNameConverterStrategyProvider->has(
            $this->propertyNameConverterStrategyKey
        );

        static::assertFalse($hasStrategy);
    }

    /**
     * @return void
     */
    public function testGetNonExistingStrategy(): void
    {
        $strategy = $this->propertyNameConverterStrategyProvider->get(
            $this->propertyNameConverterStrategyKey
        );

        static::assertNull($strategy);
    }

    /**
     * @return void
     */
    public function testRemoveNonExistingStrategy(): void
    {
        $result = $this->propertyNameConverterStrategyProvider->remove(
            $this->propertyNameConverterStrategyKey
        );

        static::assertEquals($this->propertyNameConverterStrategyProvider, $result);
    }

    /**
     * @return void
     */
    public function testAddAndGetStrategy(): void
    {
        static::assertEquals(
            $this->propertyNameConverterStrategyProvider,
            $this->propertyNameConverterStrategyProvider->add(
                $this->propertyNameConverterStrategyKey,
                $this->propertyNameConverterStrategyMock
            )
        );

        $strategy = $this->propertyNameConverterStrategyProvider->get(
            $this->propertyNameConverterStrategyKey
        );

        static::assertEquals($this->propertyNameConverterStrategyMock, $strategy);
    }

    /**
     * @return void
     */
    public function testGetAllStrategies(): void
    {
        $strategies = $this->propertyNameConverterStrategyProvider->getAll();

        static::assertIsArray($strategies);
        static::assertCount(0, $strategies);
    }
}
