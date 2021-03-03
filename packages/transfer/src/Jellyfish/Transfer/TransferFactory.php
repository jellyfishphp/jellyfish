<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Jellyfish\Finder\FinderFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionMapLoader;
use Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionMapMapper;
use Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionMapMerger;
use Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface;
use Jellyfish\Transfer\Definition\DefinitionFinder;
use Jellyfish\Transfer\Definition\DefinitionFinderInterface;
use Jellyfish\Transfer\Generator\ClassGenerator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use function sprintf;

class TransferFactory
{
    /**
     * @var \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    protected $filesystemFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacade;

    /**
     * @var \Jellyfish\Finder\FinderFacadeInterface
     */
    protected $finderFacade;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var \Jellyfish\Transfer\TransferGeneratorInterface
     */
    protected $transferGenerate;

    /**
     * @var \Jellyfish\Transfer\TransferCleanerInterface
     */
    protected $transferCleaner;

    /**
     * @param \Jellyfish\Filesystem\FilesystemFacadeInterface $filesystemFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     * @param \Jellyfish\Finder\FinderFacadeInterface $finderFacade
     * @param string $rootDir
     */
    public function __construct(
        FilesystemFacadeInterface $filesystemFacade,
        SerializerFacadeInterface $serializerFacade,
        FinderFacadeInterface $finderFacade,
        string $rootDir
    ) {
        $this->filesystemFacade = $filesystemFacade;
        $this->finderFacade = $finderFacade;
        $this->rootDir = $rootDir;
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @return \Jellyfish\Transfer\TransferGeneratorInterface
     */
    public function getTransferGenerator(): TransferGeneratorInterface
    {
        if ($this->transferGenerate === null) {
            $this->transferGenerate = new TransferGenerator(
                $this->createClassDefinitionMapLoader(),
                $this->createClassGenerators()
            );
        }

        return $this->transferGenerate;
    }

    /**
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface
     */
    protected function createClassDefinitionMapLoader(): ClassDefinitionMapLoaderInterface
    {
        return new ClassDefinitionMapLoader(
            $this->createDefinitionFinder(),
            $this->filesystemFacade,
            $this->createClassDefinitionMapMapper(),
            $this->createClassDefinitionMapMerger()
        );
    }

    /**
     * @return \Jellyfish\Transfer\Definition\DefinitionFinderInterface
     */
    protected function createDefinitionFinder(): DefinitionFinderInterface
    {
        return new DefinitionFinder($this->finderFacade, $this->rootDir);
    }

    /**
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface
     */
    protected function createClassDefinitionMapMapper(): ClassDefinitionMapMapperInterface
    {
        return new ClassDefinitionMapMapper($this->serializerFacade);
    }

    /**
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface
     */
    protected function createClassDefinitionMapMerger(): ClassDefinitionMapMergerInterface
    {
        return new ClassDefinitionMapMerger();
    }

    /**
     * @return \Jellyfish\Transfer\Generator\ClassGeneratorInterface[]
     */
    protected function createClassGenerators(): array
    {
        $targetDirectory = $this->getTargetDirectory();
        $twigEnvironment = $this->createTwigEnvironment();

        return [
            new ClassGenerator(
                $this->filesystemFacade,
                $twigEnvironment,
                $targetDirectory
            )
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
     * @return \Jellyfish\Transfer\TransferCleanerInterface
     */
    public function getTransferCleaner(): TransferCleanerInterface
    {
        if ($this->transferCleaner === null) {
            $this->transferCleaner = new TransferCleaner(
                $this->finderFacade,
                $this->filesystemFacade,
                $this->getTargetDirectory()
            );
        }

        return $this->transferCleaner;
    }

    /**
     * @return string
     */
    protected function getTargetDirectory(): string
    {
        return sprintf('%ssrc/Generated/Transfer/', $this->rootDir);
    }
}
