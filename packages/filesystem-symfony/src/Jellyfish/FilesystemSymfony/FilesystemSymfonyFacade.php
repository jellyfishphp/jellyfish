<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

use Jellyfish\Filesystem\FilesystemFacadeInterface;

class FilesystemSymfonyFacade implements FilesystemFacadeInterface
{
    /**
     * @var \Jellyfish\FilesystemSymfony\FilesystemSymfonyFactory
     */
    protected FilesystemSymfonyFactory $factory;

    /**
     * @param \Jellyfish\FilesystemSymfony\FilesystemSymfonyFactory $factory
     */
    public function __construct(FilesystemSymfonyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $path
     * @param int $mode
     *
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    public function mkdir(string $path, int $mode = 0777): FilesystemFacadeInterface
    {
        $this->factory->getFilesystem()->mkdir($path, $mode);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    public function remove(string $path): FilesystemFacadeInterface
    {
        $this->factory->getFilesystem()->remove($path);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->factory->getFilesystem()->exists($path);
    }

    /**
     * @param string $pathToFile
     * @param string $content
     *
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    public function appendToFile(string $pathToFile, string $content): FilesystemFacadeInterface
    {
        $this->factory->getFilesystem()->appendToFile($pathToFile, $content);

        return $this;
    }

    /**
     * @param string $pathToFile
     * @param string $content
     *
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    public function writeToFile(string $pathToFile, string $content): FilesystemFacadeInterface
    {
        $this->factory->getFilesystem()->writeToFile($pathToFile, $content);

        return $this;
    }

    /**
     * @param string $pathToFile
     *
     * @return string
     */
    public function readFromFile(string $pathToFile): string
    {
        return $this->factory->getFilesystem()->readFromFile($pathToFile);
    }
}
