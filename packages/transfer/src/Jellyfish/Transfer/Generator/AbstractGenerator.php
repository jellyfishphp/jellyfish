<?php

namespace Jellyfish\Transfer\Generator;

use Jellyfish\Filesystem\FilesystemInterface;
use Twig\Environment;

abstract class AbstractGenerator
{
    protected const FILE_EXTENSION = '.php';

    /**
     * @var \Jellyfish\Filesystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $targetDirectory;

    /**
     * @param \Jellyfish\Filesystem\FilesystemInterface $filesystem
     * @param \Twig\Environment $twig
     * @param string $targetDirectory
     */
    public function __construct(
        FilesystemInterface $filesystem,
        Environment $twig,
        string $targetDirectory
    ) {
        $this->twig = $twig;
        $this->targetDirectory = $targetDirectory;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $path
     *
     * @return \Jellyfish\Transfer\Generator\AbstractGenerator
     */
    protected function createDirectories(string $path): AbstractGenerator
    {
        if ($this->filesystem->exists($path)) {
            return $this;
        }

        $this->filesystem->mkdir($path, 0775);

        return $this;
    }

    /**
     * @return string
     */
    abstract protected function getTemplateName(): string;
}
