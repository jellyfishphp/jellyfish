<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Console\ConsoleConstants;
use Jellyfish\Console\ConsoleFacadeInterface;
use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Event\Command\EventQueueWorkerStartCommand;
use Jellyfish\Lock\LockConstants;
use Jellyfish\Log\LogConstants;
use Jellyfish\Process\ProcessConstants;
use Jellyfish\Queue\QueueConstants;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Uuid\UuidConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerEventFacade($container)
            ->registerCommands($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Event\EventServiceProvider
     */
    protected function registerEventFacade(Container $container): EventServiceProvider
    {
        $container->offsetSet(EventConstants::FACADE, static function (Container $container) {
            $eventFactory = new EventFactory(
                $container->offsetGet(ProcessConstants::FACADE),
                $container->offsetGet(QueueConstants::FACADE),
                $container->offsetGet(SerializerConstants::FACADE),
                $container->offsetGet(UuidConstants::FACADE),
                $container->offsetGet('root_dir')
            );

            return new EventFacade($eventFactory);
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
        $container->extend(
            ConsoleConstants::FACADE,
            static function (ConsoleFacadeInterface $consoleFacade, Container $container) {
                $consoleFacade->addCommand(
                    new EventQueueConsumeCommand(
                        $container->offsetGet(EventConstants::FACADE),
                        $container->offsetGet(LockConstants::FACADE),
                        $container->offsetGet(LogConstants::FACADE)
                    )
                );

                $consoleFacade->addCommand(
                    new EventQueueWorkerStartCommand(
                        $container->offsetGet(EventConstants::FACADE)
                    )
                );

                return $consoleFacade;
            }
        );

        return $this;
    }
}
