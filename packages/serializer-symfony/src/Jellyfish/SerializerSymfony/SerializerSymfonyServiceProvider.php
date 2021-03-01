<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use Jellyfish\Serializer\SerializerConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
