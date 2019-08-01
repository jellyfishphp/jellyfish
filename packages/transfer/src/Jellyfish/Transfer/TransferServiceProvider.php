<?php

namespace Jellyfish\Transfer;

use ArrayObject;
use Jellyfish\Transfer\Generator\ClassGenerator;
use Jellyfish\Transfer\Generator\FactoryClassGenerator;
use Jellyfish\Transfer\Command\TransferGenerateCommand;
use Jellyfish\Transfer\Definition\ClassDefinitionMapLoader;
use Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionMapMapper;
use Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionMapMerger;
use Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface;
use Jellyfish\Transfer\Definition\DefinitionFinder;
use Jellyfish\Transfer\Definition\DefinitionFinderInterface;
use Jellyfish\Transfer\Generator\FactoryRegistryGenerator;
use Jellyfish\Transfer\Generator\FactoryRegistryGeneratorInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TransferServiceProvider implements ServiceProviderInterface
{
    /**
     * @var \Twig\Environment|null
     */
    protected $twigEnvironment;

    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerCommands($pimple)
            ->registerFactories($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\TransferServiceProvider
     */
    protected function registerCommands(Container $container): TransferServiceProvider
    {
        $self = $this;

        $container->extend('commands', function (array $commands, Container $container) use ($self) {
            $commands[] = new TransferGenerateCommand(
                $self->createTransferGenerator($container),
                $self->createTransferCleaner($container),
                $container->offsetGet('logger')
            );

            return $commands;
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\TransferGeneratorInterface
     */
    protected function createTransferGenerator(Container $container): TransferGeneratorInterface
    {
        return new TransferGenerator(
            $this->createClassDefinitionMapLoader($container),
            $this->createFactoryRegistryGenerator($container),
            $this->createClassGenerators($container)
        );
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface
     */
    protected function createClassDefinitionMapLoader(Container $container): ClassDefinitionMapLoaderInterface
    {
        return new ClassDefinitionMapLoader(
            $this->createDefinitionFinder($container),
            $container->offsetGet('filesystem'),
            $this->createClassDefinitionMapMapper($container),
            $this->createClassDefinitionMapMerger()
        );
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\Definition\DefinitionFinderInterface
     */
    protected function createDefinitionFinder(Container $container): DefinitionFinderInterface
    {
        return new DefinitionFinder(
            $container->offsetGet('finder_factory'),
            $container->offsetGet('root_dir')
        );
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface
     */
    protected function createClassDefinitionMapMapper(Container $container): ClassDefinitionMapMapperInterface
    {
        return new ClassDefinitionMapMapper($container->offsetGet('serializer'));
    }

    /**
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface
     */
    protected function createClassDefinitionMapMerger(): ClassDefinitionMapMergerInterface
    {
        return new ClassDefinitionMapMerger();
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\Generator\FactoryRegistryGeneratorInterface
     */
    protected function createFactoryRegistryGenerator(Container $container): FactoryRegistryGeneratorInterface
    {
        $targetDirectory = $this->getTargetDirectory($container);
        $twigEnvironment = $this->getTwigEnvironment();

        return new FactoryRegistryGenerator($container->offsetGet('filesystem'), $twigEnvironment, $targetDirectory);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return array
     */
    protected function createClassGenerators(Container $container): array
    {
        $targetDirectory = $this->getTargetDirectory($container);
        $twigEnvironment = $this->getTwigEnvironment();

        return [
            new ClassGenerator(
                $container->offsetGet('filesystem'),
                $twigEnvironment,
                $targetDirectory
            ),
            new FactoryClassGenerator(
                $container->offsetGet('filesystem'),
                $twigEnvironment,
                $targetDirectory
            ),
        ];
    }

    /**
     * @return \Twig\Environment
     */
    protected function getTwigEnvironment(): Environment
    {
        if ($this->twigEnvironment === null) {
            $pathToTemplates = __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;
            $loader = new FilesystemLoader($pathToTemplates);
            $this->twigEnvironment = new Environment($loader, []);
        }

        return $this->twigEnvironment;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return string
     */
    protected function getTargetDirectory(Container $container): string
    {
        return $container->offsetGet('root_dir') . 'src/Generated/Transfer/';
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\TransferCleanerInterface
     */
    protected function createTransferCleaner(Container $container): TransferCleanerInterface
    {
        $targetDirectory = $this->getTargetDirectory($container);

        return new TransferCleaner(
            $container->offsetGet('finder_factory'),
            $container->offsetGet('filesystem'),
            $targetDirectory
        );
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Transfer\TransferServiceProvider
     */
    protected function registerFactories(Container $container): TransferServiceProvider
    {
        $pathToFactoryRegistry = $this->getTargetDirectory($container) . 'factory-registry.php';
        $factoryRegistry = new ArrayObject();

        if (\file_exists($pathToFactoryRegistry)) {
            include $pathToFactoryRegistry;
        }

        foreach ($factoryRegistry as $factoryId => $factory) {
            $container->offsetSet((string)$factoryId, function () use ($factory) {
                return $factory;
            });
        }

        return $this;
    }
}
