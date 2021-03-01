<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Jellyfish\Console\ConsoleConstants;
use Jellyfish\Console\ConsoleFacadeInterface;
use Jellyfish\Filesystem\FilesystemConstants;
use Jellyfish\Finder\FinderConstants;
use Jellyfish\Log\LogConstants;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Transfer\Command\TransferGenerateCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TransferServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerTransferFacade($pimple)
            ->registerCommands($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\TransferServiceProvider
     */
    protected function registerTransferFacade(Container $container): TransferServiceProvider
    {
        $container->offsetSet(TransferConstants::FACADE, static function (Container $container) {
            $transferFactory = new TransferFactory(
                $container->offsetGet(FilesystemConstants::FACADE),
                $container->offsetGet(SerializerConstants::FACADE),
                $container->offsetGet(FinderConstants::FACADE),
                $container->offsetGet('root_dir')
            );

            return new TransferFacade($transferFactory);
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\TransferServiceProvider
     */
    protected function registerCommands(Container $container): TransferServiceProvider
    {
        $container->extend(
            ConsoleConstants::FACADE,
            static function (ConsoleFacadeInterface $consoleFacade, Container $container) {
                $consoleFacade->addCommand(
                    new TransferGenerateCommand(
                        $container->offsetGet(TransferConstants::FACADE),
                        $container->offsetGet(LogConstants::FACADE)
                    )
                );

                return $consoleFacade;
            }
        );

        return $this;
    }
}
