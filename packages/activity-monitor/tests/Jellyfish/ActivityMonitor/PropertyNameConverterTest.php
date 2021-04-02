<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Codeception\Test\Unit;

class PropertyNameConverterTest extends Unit
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $camelCasedPropertyName;

    /**
     * @var string
     */
    protected $snakeCasedPropertyName;

    /**
     * @var \Jellyfish\ActivityMonitor\PropertyNameConverter
     */
    protected $propertyNameConverter;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->format = 'json';
        $this->class = 'Generated\\Transfer\\Pm2\\FooBar';
        $this->camelCasedPropertyName = 'fooBarId';
        $this->snakeCasedPropertyName = 'foo_bar_id';

        $this->propertyNameConverter = new PropertyNameConverter();
    }

    /**
     * @return void
     */
    public function testConvertAfterNormalize(): void
    {
        static::assertEquals(
            $this->snakeCasedPropertyName,
            $this->propertyNameConverter->convertAfterNormalize(
                $this->camelCasedPropertyName,
                $this->class,
                $this->format
            )
        );
    }

    /**
     * @return void
     */
    public function testConvertAfterNormalizeWithInvalidClass(): void
    {
        $class = 'Generated\\Transfer\\Foo\\FooBar';

        static::assertEquals(
            null,
            $this->propertyNameConverter->convertAfterNormalize($this->camelCasedPropertyName, $class, $this->format)
        );
    }

    /**
     * @return void
     */
    public function testConvertAfterNormalizeWithNullableClass(): void
    {
        static::assertEquals(
            null,
            $this->propertyNameConverter->convertAfterNormalize($this->camelCasedPropertyName, null, $this->format)
        );
    }

    /**
     * @return void
     */
    public function testConvertAfterDenormalize(): void
    {
        static::assertEquals(
            $this->camelCasedPropertyName,
            $this->propertyNameConverter->convertAfterDenormalize(
                $this->snakeCasedPropertyName,
                $this->class,
                $this->format
            )
        );
    }

    /**
     * @return void
     */
    public function testConvertAfterDenormalizeWithInvalidClass(): void
    {
        $class = 'Generated\\Transfer\\Foo\\FooBar';

        static::assertEquals(
            null,
            $this->propertyNameConverter->convertAfterDenormalize($this->snakeCasedPropertyName, $class, $this->format)
        );
    }

    /**
     * @return void
     */
    public function testConvertAfterDenormalizeWithNullableClass(): void
    {
        static::assertEquals(
            null,
            $this->propertyNameConverter->convertAfterNormalize($this->snakeCasedPropertyName, null, $this->format)
        );
    }
}
