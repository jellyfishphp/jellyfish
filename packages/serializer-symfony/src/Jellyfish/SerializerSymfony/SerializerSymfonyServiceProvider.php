<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use Jellyfish\Serializer\SerializerConstants;
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

        $container->offsetSet(
            SerializerConstants::CONTAINER_KEY_SERIALIZER,
            static function (Container $container) use ($self) {
                return new Serializer(
                    $self->createSymfonySerializer($container)
                );
            }
        );

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Symfony\Component\Serializer\SerializerInterface
     */
    protected function createSymfonySerializer(Container $container): SymfonySerializerInterface
    {
        $strategyProvider = $container->offsetGet(
            SerializerConstants::CONTAINER_KEY_PROPERTY_NAME_CONVERTER_STRATEGY_PROVIDER
        );

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
