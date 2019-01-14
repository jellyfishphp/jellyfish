<?php

namespace Jellyfish\Event;

use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Event\Command\EventQueueWorkerStartCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->createEventFactory($pimple);
        $this->createEventQueueNameGenerator($pimple);
        $this->createEventMapper($pimple);
        $this->createEventQueueProducer($pimple);
        $this->createEventQueueConsumer($pimple);
        $this->createEventDispatcher($pimple);
        $this->createEventQueueWorker($pimple);
        $this->createCommands($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createEventFactory(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('event_factory', function () {
            return new EventFactory();
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createEventQueueNameGenerator(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('event_queue_name_generator', function () {
            return new EventQueueNameGenerator();
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createEventMapper(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('event_mapper', function (Container $container) {
            return new EventMapper(
                $container->offsetGet('event_factory'),
                $container->offsetGet('message_factory'),
                $container->offsetGet('serializer')
            );
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createEventQueueProducer(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('event_queue_producer', function (Container $container) {
            return new EventQueueProducer(
                $container->offsetGet('event_mapper'),
                $container->offsetGet('event_queue_name_generator'),
                $container->offsetGet('queue_client')
            );
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createEventQueueConsumer(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('event_queue_consumer', function (Container $container) {
            return new EventQueueConsumer(
                $container->offsetGet('process_factory'),
                $container->offsetGet('event_mapper'),
                $container->offsetGet('event_queue_name_generator'),
                $container->offsetGet('queue_client'),
                $container->offsetGet('root_dir')
            );
        });

        return $this;
    }


    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createEventDispatcher(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('event_dispatcher', function (Container $container) {
            return new EventDispatcher(
                $container->offsetGet('event_queue_producer')
            );
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createEventQueueWorker(Container $container): ServiceProviderInterface
    {
        $container->offsetSet('event_queue_worker', function (Container $container) {
            return new EventQueueWorker(
                $container->offsetGet('event_dispatcher'),
                $container->offsetGet('event_queue_consumer')
            );
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createCommands(Container $container): ServiceProviderInterface
    {
        $container->extend('commands', function (array $commands, Container $container) {
            $commands[] = new EventQueueConsumeCommand(
                $container->offsetGet('event_dispatcher'),
                $container->offsetGet('event_queue_consumer'),
                $container->offsetGet('lock_factory')
            );

            $commands[] = new EventQueueWorkerStartCommand(
                $container->offsetGet('event_queue_worker')
            );

            return $commands;
        });

        return $this;
    }
}
