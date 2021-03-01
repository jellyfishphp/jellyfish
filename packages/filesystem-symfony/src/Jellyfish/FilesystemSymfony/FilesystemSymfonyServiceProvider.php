<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

use Jellyfish\Filesystem\FilesystemConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FilesystemSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerFilesystemFacade($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\FilesystemSymfony\FilesystemSymfonyServiceProvider
     */
    protected function registerFilesystemFacade(Container $container): FilesystemSymfonyServiceProvider
    {
        $container->offsetSet(FilesystemConstants::FACADE, static function () {
            return new FilesystemSymfonyFacade(
                new FilesystemSymfonyFactory()
            );
        });

        return $this;
    }
}
