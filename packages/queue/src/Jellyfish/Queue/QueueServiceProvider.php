<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

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
        $this->registerMessageFactory($pimple)
            ->registerMessageMapper($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Queue\QueueServiceProvider
     */
    protected function registerMessageFactory(Container $container): QueueServiceProvider
    {
        $container->offsetSet('message_factory', function () {
            return new MessageFactory();
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Queue\QueueServiceProvider
     */
    protected function registerMessageMapper(Container $container): QueueServiceProvider
    {
        $container->offsetSet('message_mapper', function (Container $container) {
            return new MessageMapper(
                $container->offsetGet('serializer')
            );
        });

        return $this;
    }
}
