<?php

declare(strict_types=1);

namespace Jellyfish\Filesystem;

interface FilesystemFacadeInterface
{
    /**
     * @param string $path
     * @param int $mode
     *
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    public function mkdir(string $path, int $mode = 0777): FilesystemFacadeInterface;

    /**
     * @param string $path
     *
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    public function remove(string $path): FilesystemFacadeInterface;

    /**
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * @param string $pathToFile
     * @param string $content
     *
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    public function appendToFile(string $pathToFile, string $content): FilesystemFacadeInterface;

    /**
     * @param string $pathToFile
     * @param string $content
     *
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    public function writeToFile(string $pathToFile, string $content): FilesystemFacadeInterface;

    /**
     * @param string $pathToFile
     *
     * @return string
     */
    public function readFromFile(string $pathToFile): string;
}
