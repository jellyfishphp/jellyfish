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
        $this->registerSerializerFacade($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\SerializerSymfony\SerializerSymfonyServiceProvider
     */
    protected function registerSerializerFacade(Container $container): SerializerSymfonyServiceProvider
    {
        $container->offsetSet(SerializerConstants::FACADE, static function () {
            return new SerializerSymfonyFacade(
                new SerializerSymfonyFactory()
            );
        });

        return $this;
    }
}
