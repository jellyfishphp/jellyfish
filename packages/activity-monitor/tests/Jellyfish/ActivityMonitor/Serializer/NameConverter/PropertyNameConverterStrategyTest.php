<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor\Serializer\NameConverter;

use Codeception\Test\Unit;
use Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface;

class PropertyNameConverterStrategyTest extends Unit
{
    /**
     * @var string
     */
    protected string $class;

    /**
     * @var string
     */
    protected string $format;

    /**
     * @var string
     */
    protected string $camelCasedPropertyName;

    /**
     * @var string
     */
    protected string $snakeCasedPropertyName;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMonitorFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $activityMonitorFacadeMock;

    /**
     * @var \Jellyfish\ActivityMonitor\Serializer\NameConverter\PropertyNameConverterStrategy
     */
    protected PropertyNameConverterStrategy $propertyNameConverterStrategy;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->class = 'Generated\\Transfer\\Pm2\\FooBar';
        $this->format = 'json';
        $this->camelCasedPropertyName = 'fooBarId';
        $this->snakeCasedPropertyName = 'foo_bar_id';

        $this->activityMonitorFacadeMock = $this->getMockBuilder(ActivityMonitorFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyNameConverterStrategy = new PropertyNameConverterStrategy(
            $this->activityMonitorFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testConvertAfterNormalize(): void
    {
        $this->activityMonitorFacadeMock->expects(static::atLeastOnce())
            ->method('convertPropertyNameAfterNormalize')
            ->with($this->camelCasedPropertyName, $this->class, $this->format)
            ->willReturn($this->snakeCasedPropertyName);

        static::assertEquals(
            $this->snakeCasedPropertyName,
            $this->propertyNameConverterStrategy->convertAfterNormalize(
                $this->camelCasedPropertyName,
                $this->class,
                $this->format
            )
        );
    }

    /**
     * @return void
     */
    public function testConvertAfterDenormalize(): void
    {
        $this->activityMonitorFacadeMock->expects(static::atLeastOnce())
            ->method('convertPropertyNameAfterDenormalize')
            ->with($this->snakeCasedPropertyName, $this->class, $this->format)
            ->willReturn($this->camelCasedPropertyName);

        static::assertEquals(
            $this->camelCasedPropertyName,
            $this->propertyNameConverterStrategy->convertAfterDenormalize(
                $this->snakeCasedPropertyName,
                $this->class,
                $this->format
            )
        );
    }
}
