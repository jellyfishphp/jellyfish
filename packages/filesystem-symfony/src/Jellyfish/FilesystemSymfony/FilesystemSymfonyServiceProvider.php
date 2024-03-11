<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * @see \Jellyfish\FilesystemSymfony\FilesystemSymfonyServiceProviderTest
 */
class FilesystemSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerFilesystem($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\FilesystemSymfony\FilesystemSymfonyServiceProvider
     */
    protected function registerFilesystem(Container $container): FilesystemSymfonyServiceProvider
    {
        $container->offsetSet('filesystem', static function () : \Jellyfish\FilesystemSymfony\Filesystem {
            $symfonyFilesystem = new SymfonyFilesystem();
            return new Filesystem($symfonyFilesystem);
        });

        return $this;
    }
}
