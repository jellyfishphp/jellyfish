<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Config\ConfigConstants;
use Jellyfish\Queue\QueueConstants;
use Jellyfish\Serializer\SerializerConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueRabbitMqServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerQueueFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\QueueRabbitMq\QueueRabbitMqServiceProvider
     */
    protected function registerQueueFacade(Container $container): QueueRabbitMqServiceProvider
    {
        $container->offsetSet(QueueConstants::FACADE, static function (Container $container) {
            $queueRabbitMqFactory = new QueueRabbitMqFactory(
                $container->offsetGet(ConfigConstants::FACADE),
                $container->offsetGet(SerializerConstants::FACADE)
            );

            return new QueueRabbitMqFacade($queueRabbitMqFactory);
        });

        return $this;
    }
}
