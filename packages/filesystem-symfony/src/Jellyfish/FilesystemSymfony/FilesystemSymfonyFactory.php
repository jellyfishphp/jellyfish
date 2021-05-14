<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class FilesystemSymfonyFactory
{
    /**
     * @var \Jellyfish\FilesystemSymfony\FilesystemInterface|null
     */
    protected ?FilesystemInterface $filesystem = null;

    /**
     * @return \Jellyfish\FilesystemSymfony\FilesystemInterface
     */
    public function getFilesystem(): FilesystemInterface
    {
        if ($this->filesystem === null) {
            $this->filesystem = new Filesystem($this->createSymfonyFilesystem());
        }

        return $this->filesystem;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function createSymfonyFilesystem(): SymfonyFilesystem
    {
        return new SymfonyFilesystem();
    }
}
