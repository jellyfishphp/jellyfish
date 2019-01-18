<?php

namespace Jellyfish\Queue;

use Jellyfish\Serializer\SerializerInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple A container instance
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $self = $this;

        $pimple->offsetSet('message_factory', function () use ($self) {
            return $self->createMessageFactory();
        });

        $pimple->offsetSet('message_mapper', function (Container $container) use ($self) {
            return $self->createMessageMapper(
                $container->offsetGet('serializer')
            );
        });
    }

    /**
     * @param \Jellyfish\Serializer\SerializerInterface $serializer
     *
     * @return \Jellyfish\Queue\MessageMapperInterface
     */
    protected function createMessageMapper(
        SerializerInterface $serializer
    ): MessageMapperInterface {
        return new MessageMapper($serializer);
    }

    /**
     * @return \Jellyfish\Queue\MessageFactoryInterface
     */
    protected function createMessageFactory(): MessageFactoryInterface
    {
        return new MessageFactory();
    }
}
