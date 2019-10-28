<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverter;
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
        $this->registerSerializer($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\SerializerSymfony\SerializerSymfonyServiceProvider
     */
    protected function registerSerializer(Container $container): SerializerSymfonyServiceProvider
    {
        $self = $this;

        $container->offsetSet('serializer', function (Container $container) use ($self) {
            return new Serializer(
                $self->createSymfonySerializer($container)
            );
        });

        return $this;
    }

    /**
     * @return \Symfony\Component\Serializer\SerializerInterface
     */
    protected function createSymfonySerializer(Container $container): SymfonySerializerInterface
    {
        $strategyProvider = $container->offsetGet('serializer_property_name_converter_strategy_provider');

        $normalizer = [
            new ObjectNormalizer(
                null,
                new PropertyNameConverter($strategyProvider),
                null,
                new PhpDocExtractor()
            ),
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
