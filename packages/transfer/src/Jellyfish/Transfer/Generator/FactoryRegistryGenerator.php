<?php

namespace Jellyfish\Transfer\Generator;

class FactoryRegistryGenerator extends AbstractGenerator implements FactoryRegistryGeneratorInterface
{
    protected const FILE_NAME = 'factory-registry';
    protected const TEMPLATE_NAME = 'factory-registry.twig';

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface[] $classDefinitionMap
     *
     * @return \Jellyfish\Transfer\Generator\FactoryRegistryGeneratorInterface
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function generate(array $classDefinitionMap): FactoryRegistryGeneratorInterface
    {
        $file = $this->getFile();
        $fileContent = $this->twig->render($this->getTemplateName(), [
            'classDefinitionMap' => $classDefinitionMap
        ]);

        $this->createDirectories($this->targetDirectory);

        $this->filesystem->writeToFile($this->targetDirectory . $file, $fileContent);

        return $this;
    }

    /**
     * @return string
     */
    protected function getFile(): string
    {
        return static::FILE_NAME . static::FILE_EXTENSION;
    }

    /**
     * @return string
     */
    protected function getTemplateName(): string
    {
        return static::TEMPLATE_NAME;
    }
}
