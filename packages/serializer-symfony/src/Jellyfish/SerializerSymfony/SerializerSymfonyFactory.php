<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverter;
use Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterInterface;
use Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProvider;
use Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class SerializerSymfonyFactory
{
    /**
     * @var \Jellyfish\SerializerSymfony\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    protected $propertyNameConverterStrategyProvider;

    public function getSerializer(): SerializerInterface
    {
        if ($this->serializer === null) {
            $this->serializer = new Serializer($this->createSymfonySerializer());
        }

        return $this->serializer;
    }

    /**
     * @return \Symfony\Component\Serializer\SerializerInterface
     */
    protected function createSymfonySerializer(): SymfonySerializerInterface
    {
        return new SymfonySerializer(
            array_merge($this->createNormalizers(), $this->createDenormalizers()),
            $this->createEncoders()
        );
    }

    /**
     * @return \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]
     */
    protected function createNormalizers(): array
    {
        return [
            new ObjectNormalizer(null, $this->createPropertyNameConverter(), null, new PhpDocExtractor())
        ];
    }

    /**
     * @return \Symfony\Component\Serializer\Normalizer\DenormalizerInterface[]
     */
    protected function createDenormalizers(): array
    {
        return [
            new ArrayDenormalizer()
        ];
    }

    /**
     * @return \Symfony\Component\Serializer\Encoder\EncoderInterface[]
     */
    protected function createEncoders(): array
    {
        return [
            new JsonEncoder(),
            new XmlEncoder()
        ];
    }

    /**
     * @return \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterInterface
     */
    protected function createPropertyNameConverter(): PropertyNameConverterInterface
    {
        return new PropertyNameConverter($this->getPropertyNameConverterStrategyProvider());
    }

    /**
     * @return \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProviderInterface
     */
    public function getPropertyNameConverterStrategyProvider(): PropertyNameConverterStrategyProviderInterface
    {
        if ($this->propertyNameConverterStrategyProvider === null) {
            $this->propertyNameConverterStrategyProvider = new PropertyNameConverterStrategyProvider();
        }

        return $this->propertyNameConverterStrategyProvider;
    }
}
