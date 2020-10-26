<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Event\Command\EventQueueWorkerStartCommand;
use Jellyfish\Queue\QueueConstants;
use Jellyfish\Uuid\UuidConstants;
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
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerEventFactory($container)
            ->registerEventDispatcher($container)
            ->registerCommands($container)
            ->registerDefaultEventErrorHandlers($container);
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
                $container->offsetGet(QueueConstants::CONTAINER_KEY_QUEUE_CLIENT),
                $container->offsetGet(QueueConstants::CONTAINER_KEY_DESTINATION_FACTORY)
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
                $container->offsetGet(QueueConstants::CONTAINER_KEY_QUEUE_CLIENT),
                $container->offsetGet(QueueConstants::CONTAINER_KEY_DESTINATION_FACTORY),
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
        $container->offsetSet(EventConstants::CONTAINER_KEY_EVENT_FACTORY, static function (Container $container) {
            return new EventFactory($container->offsetGet(UuidConstants::CONTAINER_KEY_UUID_GENERATOR));
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

        $container->offsetSet(
            EventConstants::CONTAINER_KEY_EVENT_DISPATCHER,
            static function (Container $container) use ($self) {
                return new EventDispatcher(
                    new EventListenerProvider(),
                    $self->createEventQueueProducer($container)
                );
            }
        );

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

        $container->extend('commands', static function (array $commands, Container $container) use ($self) {
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

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Event\EventServiceProvider
     */
    protected function registerDefaultEventErrorHandlers(Container $container): EventServiceProvider
    {
        $container->offsetSet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS, static function () {
            return [];
        });

        return $this;
    }
}
