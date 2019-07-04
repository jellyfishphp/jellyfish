<?php

namespace Jellyfish\FilesystemSymfony;

use Jellyfish\Filesystem\FilesystemInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem implements FilesystemInterface
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $symfonyFilesystem;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $symfonyFilesystem
     */
    public function __construct(SymfonyFilesystem $symfonyFilesystem)
    {
        $this->symfonyFilesystem = $symfonyFilesystem;
    }

    /**
     * @param string $path
     * @param int $mode
     *
     * @return \Jellyfish\Filesystem\FilesystemInterface
     */
    public function mkdir(string $path, int $mode = 0777): FilesystemInterface
    {
        $this->symfonyFilesystem->mkdir($path, $mode);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return \Jellyfish\Filesystem\FilesystemInterface
     */
    public function remove(string $path): FilesystemInterface
    {
        $this->symfonyFilesystem->remove($path);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->symfonyFilesystem->exists($path);
    }

    /**
     * @param string $pathToFile
     * @param string $content
     *
     * @return \Jellyfish\Filesystem\FilesystemInterface
     */
    public function appendToFile(string $pathToFile, string $content): FilesystemInterface
    {
        if (false === @\file_put_contents($pathToFile, $content, FILE_APPEND)) {
            throw new IOException(sprintf('Failed to write file "%s".', $pathToFile), 0, null, $pathToFile);
        }

        return $this;
    }

    /**
     * @param string $pathToFile
     * @param string $content
     *
     * @return \Jellyfish\Filesystem\FilesystemInterface
     */
    public function writeToFile(string $pathToFile, string $content): FilesystemInterface
    {
        if (false === @\file_put_contents($pathToFile, $content)) {
            throw new IOException(sprintf('Failed to write file "%s".', $pathToFile), 0, null, $pathToFile);
        }

        return $this;
    }
}
