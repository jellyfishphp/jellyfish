<?php

namespace Jellyfish\SerializerSymfony\NameConverter;

use Codeception\Test\Unit;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface;

class PropertyNameConverterTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    protected $propertyNameConverterStrategyProviderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject[]|\Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface[]
     */
    protected $propertyNameConverterStrategyMocks;

    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverter
     */
    protected $propertyNameConverter;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->propertyNameConverterStrategyProviderMock = $this->getMockBuilder(PropertyNameConverterStrategyProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyNameConverterStrategyMocks = [
            $this->getMockBuilder(PropertyNameConverterStrategyInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(PropertyNameConverterStrategyInterface::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        $this->propertyName = 'property';
        $this->class = '\Class';
        $this->format = 'json';

        $this->propertyNameConverter = new PropertyNameConverter($this->propertyNameConverterStrategyProviderMock);
    }

    /**
     * @return void
     */
    public function testNormalize(): void
    {
        $expectedPropertyName = 'convertedProperty';

        $this->propertyNameConverterStrategyProviderMock->expects($this->atLeastOnce())
            ->method('getAllStrategies')
            ->willReturn($this->propertyNameConverterStrategyMocks);

        $this->propertyNameConverterStrategyMocks[0]->expects($this->atLeastOnce())
            ->method('convertAfterNormalize')
            ->with($this->propertyName, $this->class, $this->format)
            ->willReturn(null);

        $this->propertyNameConverterStrategyMocks[1]->expects($this->atLeastOnce())
            ->method('convertAfterNormalize')
            ->with($this->propertyName, $this->class, $this->format)
            ->willReturn($expectedPropertyName);

        $actualPropertyName = $this->propertyNameConverter
            ->normalize($this->propertyName, $this->class, $this->format);

        $this->assertEquals($expectedPropertyName, $actualPropertyName);
    }

    /**
     * @return void
     */
    public function testNormalizeWithFallback(): void
    {
        $expectedPropertyName = $this->propertyName;

        $this->propertyNameConverterStrategyProviderMock->expects($this->atLeastOnce())
            ->method('getAllStrategies')
            ->willReturn($this->propertyNameConverterStrategyMocks);

        $this->propertyNameConverterStrategyMocks[0]->expects($this->atLeastOnce())
            ->method('convertAfterNormalize')
            ->with($this->propertyName, $this->class, $this->format)
            ->willReturn(null);

        $this->propertyNameConverterStrategyMocks[1]->expects($this->atLeastOnce())
            ->method('convertAfterNormalize')
            ->with($this->propertyName, $this->class, $this->format)
            ->willReturn(null);

        $actualPropertyName = $this->propertyNameConverter
            ->normalize($this->propertyName, $this->class, $this->format);

        $this->assertEquals($expectedPropertyName, $actualPropertyName);
    }

    /**
     * @return void
     */
    public function testDenormalize(): void
    {
        $expectedPropertyName = 'convertedProperty';

        $this->propertyNameConverterStrategyProviderMock->expects($this->atLeastOnce())
            ->method('getAllStrategies')
            ->willReturn($this->propertyNameConverterStrategyMocks);

        $this->propertyNameConverterStrategyMocks[0]->expects($this->atLeastOnce())
            ->method('convertAfterDenormalize')
            ->with($this->propertyName, $this->class, $this->format)
            ->willReturn(null);

        $this->propertyNameConverterStrategyMocks[1]->expects($this->atLeastOnce())
            ->method('convertAfterDenormalize')
            ->with($this->propertyName, $this->class, $this->format)
            ->willReturn($expectedPropertyName);

        $actualPropertyName = $this->propertyNameConverter
            ->denormalize($this->propertyName, $this->class, $this->format);

        $this->assertEquals($expectedPropertyName, $actualPropertyName);
    }

    /**
     * @return void
     */
    public function testDenormalizeWithFallback(): void
    {
        $expectedPropertyName = $this->propertyName;

        $this->propertyNameConverterStrategyProviderMock->expects($this->atLeastOnce())
            ->method('getAllStrategies')
            ->willReturn($this->propertyNameConverterStrategyMocks);

        $this->propertyNameConverterStrategyMocks[0]->expects($this->atLeastOnce())
            ->method('convertAfterDenormalize')
            ->with($this->propertyName, $this->class, $this->format)
            ->willReturn(null);

        $this->propertyNameConverterStrategyMocks[1]->expects($this->atLeastOnce())
            ->method('convertAfterDenormalize')
            ->with($this->propertyName, $this->class, $this->format)
            ->willReturn(null);

        $actualPropertyName = $this->propertyNameConverter
            ->denormalize($this->propertyName, $this->class, $this->format);

        $this->assertEquals($expectedPropertyName, $actualPropertyName);
    }
}
