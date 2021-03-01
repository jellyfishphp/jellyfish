<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

interface FilesystemInterface
{
    /**
     * @param string $path
     * @param int $mode
     *
     * @return \Jellyfish\FilesystemSymfony\FilesystemInterface
     */
    public function mkdir(string $path, int $mode = 0777): FilesystemInterface;

    /**
     * @param string $path
     *
     * @return \Jellyfish\FilesystemSymfony\FilesystemInterface
     */
    public function remove(string $path): FilesystemInterface;

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
     * @return \Jellyfish\FilesystemSymfony\FilesystemInterface
     */
    public function appendToFile(string $pathToFile, string $content): FilesystemInterface;

    /**
     * @param string $pathToFile
     * @param string $content
     *
     * @return \Jellyfish\FilesystemSymfony\FilesystemInterface
     */
    public function writeToFile(string $pathToFile, string $content): FilesystemInterface;

    /**
     * @param string $pathToFile
     *
     * @return string
     */
    public function readFromFile(string $pathToFile): string;
}
