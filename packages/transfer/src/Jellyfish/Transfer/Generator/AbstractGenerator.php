<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Generator;

use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Twig\Environment;

abstract class AbstractGenerator
{
    protected const FILE_EXTENSION = '.php';

    /**
     * @var \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    protected $filesystemFacade;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $targetDirectory;

    /**
     * @param \Jellyfish\Filesystem\FilesystemFacadeInterface $filesystemFacade
     * @param \Twig\Environment $twig
     * @param string $targetDirectory
     */
    public function __construct(
        FilesystemFacadeInterface $filesystemFacade,
        Environment $twig,
        string $targetDirectory
    ) {
        $this->twig = $twig;
        $this->targetDirectory = $targetDirectory;
        $this->filesystemFacade = $filesystemFacade;
    }

    /**
     * @param string $path
     *
     * @return \Jellyfish\Transfer\Generator\AbstractGenerator
     */
    protected function createDirectories(string $path): AbstractGenerator
    {
        if ($this->filesystemFacade->exists($path)) {
            return $this;
        }

        $this->filesystemFacade->mkdir($path, 0775);

        return $this;
    }

    /**
     * @return string
     */
    abstract protected function getTemplateName(): string;
}
