<?php

namespace Jellyfish\SerializerSymfony;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class SerializerSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->createSerializer($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createSerializer(Container $container): ServiceProviderInterface
    {
        $self = $this;

        $container->offsetSet('serializer', function () use ($self) {
            return new Serializer(
                $self->createSymfonySerializer()
            );
        });

        return $this;
    }

    /**
     * @return \Symfony\Component\Serializer\SerializerInterface
     */
    protected function createSymfonySerializer(): SymfonySerializerInterface
    {
        $normalizer = [
            new ObjectNormalizer(null, null, null, new PhpDocExtractor()),
            new ArrayDenormalizer()
        ];

        $encoders = [
            new JsonEncoder(),
            new XmlEncoder()
        ];

        return new SymfonySerializer(
            $normalizer,
            $encoders
        );
    }
}
