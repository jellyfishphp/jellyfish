<?php

namespace Jellyfish\Event;

use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Event\Command\EventQueueWorkerStartCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    /**
     * @var \Jellyfish\Event\EventQueueNameGeneratorInterface|null
     */
    protected $eventQueueNameGenerator;

    /**
     * @var \Jellyfish\Event\EventMapperInterface
     */
    protected $eventMapper;

    /**
     * @var \Jellyfish\Event\EventQueueProducerInterface
     */
    protected $eventQueueProducer;

    /**
     * @var \Jellyfish\Event\EventQueueConsumerInterface
     */
    protected $eventQueueConsumer;

    /**
     * @var \Jellyfish\Event\EventQueueWorkerInterface
     */
    protected $eventQueueWorker;

    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerEventFactory($pimple)
            ->registerEventDispatcher($pimple)
            ->registerCommands($pimple);
    }

    /**
     * @return \Jellyfish\Event\EventQueueNameGeneratorInterface
     */
    protected function createEventQueueNameGenerator(): EventQueueNameGeneratorInterface
    {
        if ($this->eventQueueNameGenerator === null) {
            $this->eventQueueNameGenerator = new EventQueueNameGenerator();
        }

        return $this->eventQueueNameGenerator;
    }

    /**
     * @param \Pimple\Container $container
     * @return \Jellyfish\Event\EventMapperInterface
     */
    protected function createEventMapper(Container $container): EventMapperInterface
    {
        if ($this->eventMapper === null) {
            $this->eventMapper = new EventMapper(
                $container->offsetGet('event_factory'),
                $container->offsetGet('message_factory'),
                $container->offsetGet('serializer')
            );
        }

        return $this->eventMapper;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Event\EventQueueProducerInterface
     */
    protected function createEventQueueProducer(Container $container): EventQueueProducerInterface
    {
        if ($this->eventQueueProducer === null) {
            $this->eventQueueProducer = new EventQueueProducer(
                $this->createEventMapper($container),
                $this->createEventQueueNameGenerator(),
                $container->offsetGet('queue_client')
            );
        }

        return $this->eventQueueProducer;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Event\EventQueueConsumerInterface
     */
    protected function createEventQueueConsumer(Container $container): EventQueueConsumerInterface
    {
        if ($this->eventQueueConsumer === null) {
            $this->eventQueueConsumer = new EventQueueConsumer(
                $container->offsetGet('process_factory'),
                $this->createEventMapper($container),
                $this->createEventQueueNameGenerator(),
                $container->offsetGet('queue_client'),
                $container->offsetGet('root_dir')
            );
        }

        return $this->eventQueueConsumer;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Event\EventQueueWorkerInterface
     */
    protected function createEventQueueWorker(Container $container): EventQueueWorkerInterface
    {
        if ($this->eventQueueWorker === null) {
            $this->eventQueueWorker = new EventQueueWorker(
                $container->offsetGet('event_dispatcher')->getEventListenerProvider(),
                $this->createEventQueueConsumer($container)
            );
        }

        return $this->eventQueueWorker;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Event\EventServiceProvider
     */
    protected function registerEventFactory(Container $container): EventServiceProvider
    {
        $container->offsetSet('event_factory', function () {
            return new EventFactory();
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Event\EventServiceProvider
     */
    protected function registerEventDispatcher(Container $container): EventServiceProvider
    {
        $self = $this;

        $container->offsetSet('event_dispatcher', function (Container $container) use ($self) {
            return new EventDispatcher(
                new EventListenerProvider(),
                $self->createEventQueueProducer($container)
            );
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Event\EventServiceProvider
     */
    protected function registerCommands(Container $container): EventServiceProvider
    {
        $self = $this;

        $container->extend('commands', function (array $commands, Container $container) use ($self) {
            $commands[] = new EventQueueConsumeCommand(
                $container->offsetGet('event_dispatcher')->getEventListenerProvider(),
                $self->createEventQueueConsumer($container),
                $container->offsetGet('lock_factory'),
                $container->offsetGet('logger')
            );

            $commands[] = new EventQueueWorkerStartCommand(
                $self->createEventQueueWorker($container)
            );

            return $commands;
        });

        return $this;
    }
}
