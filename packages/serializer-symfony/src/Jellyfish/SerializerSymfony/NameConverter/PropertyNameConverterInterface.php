<?php

namespace Jellyfish\SerializerSymfony\NameConverter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface as SymfonyNameConverter;

interface PropertyNameConverterInterface extends SymfonyNameConverter
{
    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string
     */
    public function normalize($propertyName, ?string $class = null, ?string $format = null): string;

    /**
     * @param string $propertyName
     * @param string|null $class
     * @param string|null $format
     *
     * @return string
     */
    public function denormalize($propertyName, ?string $class = null, ?string $format = null): string;
}
