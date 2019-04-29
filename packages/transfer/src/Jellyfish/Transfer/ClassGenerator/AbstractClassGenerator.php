<?php

namespace Jellyfish\Transfer\ClassGenerator;

use Jellyfish\Filesystem\FilesystemInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionInterface;
use Twig\Environment;

abstract class AbstractClassGenerator implements ClassGeneratorInterface
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
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return \Jellyfish\Transfer\ClassGenerator\ClassGeneratorInterface
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function generate(ClassDefinitionInterface $classDefinition): ClassGeneratorInterface
    {
        $file = $this->getFile($classDefinition);
        $pathToFile = $this->getPathToFile($classDefinition);
        $fileContent = $this->twig->render($this->getTemplateName(), [
            'classDefinition' => $classDefinition
        ]);

        $this->createDirectories($pathToFile);

        $this->filesystem->writeToFile($pathToFile . $file, $fileContent);

        return $this;
    }

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return string
     */
    abstract protected function getFile(ClassDefinitionInterface $classDefinition): string;

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return string
     */
    protected function getPathToFile(ClassDefinitionInterface $classDefinition): string
    {
        $namespace = $classDefinition->getNamespace();

        if ($namespace === null) {
            return $this->targetDirectory;
        }

        $namespaceParts = \explode('\\', $namespace);

        return $this->targetDirectory . \implode(DIRECTORY_SEPARATOR, $namespaceParts) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    abstract protected function getTemplateName(): string;

    /**
     * @param string $path
     *
     * @return \Jellyfish\Transfer\ClassGenerator\ClassGeneratorInterface
     */
    protected function createDirectories(string $path): ClassGeneratorInterface
    {
        if ($this->filesystem->exists($path)) {
            return $this;
        }

        $this->filesystem->mkdir($path, 0775);

        return $this;
    }
}
