<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Generator;

use Codeception\Test\Unit;
use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionInterface;
use Twig\Environment;

class ClassGeneratorTest extends Unit
{
    /**
     * @var string
     */
    protected string $targetDirectory;

    /**
     * @var \Jellyfish\Transfer\Generator\ClassGenerator
     */
    protected ClassGenerator $classGenerator;

    /**
     * @var \Jellyfish\Filesystem\FilesystemFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filesystemFacadeMock;

    /**
     * @var \Twig\Environment|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $twigEnvironmentMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $classDefinitionMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->targetDirectory = './src/Generated/Transfer/';

        $this->filesystemFacadeMock = $this->getMockBuilder(FilesystemFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->twigEnvironmentMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classDefinitionMock = $this->getMockBuilder(ClassDefinitionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classGenerator = new ClassGenerator(
            $this->filesystemFacadeMock,
            $this->twigEnvironmentMock,
            $this->targetDirectory
        );
    }

    /**
     * @throws
     *
     * @return void
     */
    public function testGenerate(): void
    {
        $this->classDefinitionMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn('Product');

        $this->classDefinitionMock->expects(static::atLeastOnce())
            ->method('getNamespace')
            ->willReturn('Catalog');

        $this->twigEnvironmentMock->expects(static::atLeastOnce())
            ->method('render')
            ->with('class.twig', ['classDefinition' => $this->classDefinitionMock])
            ->willReturn('use ...');

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('exists')
            ->willReturn(false);

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('mkdir')
            ->with($this->targetDirectory . 'Catalog/', 0775);

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('writeToFile')
            ->with($this->targetDirectory . 'Catalog/Product.php', 'use ...');

        static::assertEquals(
            $this->classGenerator,
            $this->classGenerator->generate($this->classDefinitionMock)
        );
    }

    /**
     * @throws
     *
     * @return void
     */
    public function testGenerateDefaultNamespace(): void
    {
        $this->classDefinitionMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn('Product');

        $this->classDefinitionMock->expects(static::atLeastOnce())
            ->method('getNamespace')
            ->willReturn(null);

        $this->twigEnvironmentMock->expects(static::atLeastOnce())
            ->method('render')
            ->with('class.twig', ['classDefinition' => $this->classDefinitionMock])
            ->willReturn('use ...');

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('exists')
            ->willReturn(false);

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('mkdir')
            ->with($this->targetDirectory, 0775);

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('writeToFile')
            ->with($this->targetDirectory . 'Product.php', 'use ...');

        static::assertEquals(
            $this->classGenerator,
            $this->classGenerator->generate($this->classDefinitionMock)
        );
    }

    /**
     * @throws
     *
     * @return void
     */
    public function testGenerateWithExistingDirectory(): void
    {
        $this->classDefinitionMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn('Product');

        $this->classDefinitionMock->expects(static::atLeastOnce())
            ->method('getNamespace')
            ->willReturn(null);

        $this->twigEnvironmentMock->expects(static::atLeastOnce())
            ->method('render')
            ->with('class.twig', ['classDefinition' => $this->classDefinitionMock])
            ->willReturn('use ...');

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('exists')
            ->willReturn(true);

        $this->filesystemFacadeMock->expects(static::never())
            ->method('mkdir')
            ->with($this->targetDirectory, 0775);

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('writeToFile')
            ->with($this->targetDirectory . 'Product.php', 'use ...');

        static::assertEquals(
            $this->classGenerator,
            $this->classGenerator->generate($this->classDefinitionMock)
        );
    }
}
