<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Generator;

use Jellyfish\Transfer\Definition\ClassDefinitionInterface;

use function explode;
use function implode;

abstract class AbstractClassGenerator extends AbstractGenerator implements ClassGeneratorInterface
{
    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return \Jellyfish\Transfer\Generator\ClassGeneratorInterface
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

        $this->filesystemFacade->writeToFile($pathToFile . $file, $fileContent);

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

        $namespaceParts = explode('\\', $namespace);

        return $this->targetDirectory . implode(DIRECTORY_SEPARATOR, $namespaceParts) . DIRECTORY_SEPARATOR;
    }
}
