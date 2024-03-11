<?php

declare(strict_types = 1);

namespace Jellyfish\Serializer\NameConverter;

use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;

class PropertyNameConverterStrategyProviderTest extends Unit
{
    protected PropertyNameConverterStrategyInterface&MockObject $propertyNameConverterStrategyMock;

    protected string $propertyNameConverterStrategyKey;

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
        $this->assertEquals(
            $this->propertyNameConverterStrategyProvider,
            $this->propertyNameConverterStrategyProvider->addStrategy(
                $this->propertyNameConverterStrategyKey,
                $this->propertyNameConverterStrategyMock,
            ),
        );

        $this->assertEquals(
            $this->propertyNameConverterStrategyProvider,
            $this->propertyNameConverterStrategyProvider->removeStrategy(
                $this->propertyNameConverterStrategyKey,
            ),
        );

        $hasStrategy = $this->propertyNameConverterStrategyProvider->hasStrategy(
            $this->propertyNameConverterStrategyKey,
        );

        $this->assertFalse($hasStrategy);
    }

    /**
     * @return void
     */
    public function testHasStrategy(): void
    {
        $hasStrategy = $this->propertyNameConverterStrategyProvider->hasStrategy(
            $this->propertyNameConverterStrategyKey,
        );

        $this->assertFalse($hasStrategy);
    }

    /**
     * @return void
     */
    public function testGetNonExistingStrategy(): void
    {
        $strategy = $this->propertyNameConverterStrategyProvider->getStrategy(
            $this->propertyNameConverterStrategyKey,
        );

        $this->assertNull($strategy);
    }

    /**
     * @return void
     */
    public function testRemoveNonExistingStrategy(): void
    {
        $result = $this->propertyNameConverterStrategyProvider->removeStrategy(
            $this->propertyNameConverterStrategyKey,
        );

        $this->assertEquals($this->propertyNameConverterStrategyProvider, $result);
    }

    /**
     * @return void
     */
    public function testAddAndGetStrategy(): void
    {
        $this->assertEquals(
            $this->propertyNameConverterStrategyProvider,
            $this->propertyNameConverterStrategyProvider->addStrategy(
                $this->propertyNameConverterStrategyKey,
                $this->propertyNameConverterStrategyMock,
            ),
        );

        $strategy = $this->propertyNameConverterStrategyProvider->getStrategy(
            $this->propertyNameConverterStrategyKey,
        );

        $this->assertEquals($this->propertyNameConverterStrategyMock, $strategy);
    }

    /**
     * @return void
     */
    public function testGetAllStrategies(): void
    {
        $strategies = $this->propertyNameConverterStrategyProvider->getAllStrategies();

        $this->assertIsArray($strategies);
        $this->assertCount(0, $strategies);
    }
}
