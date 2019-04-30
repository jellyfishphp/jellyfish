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
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TransferServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerCommands($pimple);
        $this->registerFactories($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function registerCommands(Container $container): ServiceProviderInterface
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
     * @return array
     */
    protected function createClassGenerators(Container $container): array
    {
        $targetDirectory = $this->getTargetDirectory($container);
        $twigEnvironment = $this->createTwigEnvironment();

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
    protected function createTwigEnvironment(): Environment
    {
        $pathToTemplates = __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;

        $loader = new FilesystemLoader($pathToTemplates);

        return new Environment($loader, []);
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
     * @return \Pimple\ServiceProviderInterface
     */
    protected function registerFactories(Container $container): ServiceProviderInterface
    {
        $pathToFactoryRegistry = $this->getTargetDirectory($container) . 'factory-registry.php';
        $factoryRegistry = new ArrayObject();

        if (\file_exists($pathToFactoryRegistry)) {
            include $pathToFactoryRegistry;
        }

        foreach ($factoryRegistry as $factoryId => $factory) {
            $container->offsetSet($factoryId, function () use ($factory) {
                return $factory;
            });
        }

        return $this;
    }
}
