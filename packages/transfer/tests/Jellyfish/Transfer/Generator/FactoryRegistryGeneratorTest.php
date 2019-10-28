<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Generator;

use Codeception\Test\Unit;
use Jellyfish\Filesystem\FilesystemInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionInterface;
use Twig\Environment;

class FactoryRegistryGeneratorTest extends Unit
{
    /**
     * @var string
     */
    protected $targetDirectory;

    /**
     * @var \Jellyfish\Filesystem\FilesystemInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Twig\Environment|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $twigEnvironmentMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected $classDefinitionMapMock;

    /**
     * @var \Jellyfish\Transfer\Generator\FactoryRegistryGenerator
     */
    protected $factoryRegistryGenerator;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->targetDirectory = './src/Generated/Transfer/';

        $this->filesystemMock = $this->getMockBuilder(FilesystemInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->twigEnvironmentMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classDefinitionMapMock = [
            'Product' => $this->getMockBuilder(ClassDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        $this->factoryRegistryGenerator = new FactoryRegistryGenerator(
            $this->filesystemMock,
            $this->twigEnvironmentMock,
            $this->targetDirectory
        );
    }

    /**
     * @return void
     */
    public function testGenerateWithExistingDirectory(): void
    {
        $this->twigEnvironmentMock->expects($this->atLeastOnce())
            ->method('render')
            ->with('factory-registry.twig', ['classDefinitionMap' => $this->classDefinitionMapMock])
            ->willReturn('<?php');

        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('exists')
            ->willReturn(true);

        $this->filesystemMock->expects($this->never())
            ->method('mkdir')
            ->with($this->targetDirectory, 0775);

        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('writeToFile')
            ->with($this->targetDirectory . 'factory-registry.php', '<?php');

        $this->assertEquals(
            $this->factoryRegistryGenerator,
            $this->factoryRegistryGenerator->generate($this->classDefinitionMapMock)
        );
    }

    /**
     * @return void
     */
    public function testGenerate(): void
    {
        $this->twigEnvironmentMock->expects($this->atLeastOnce())
            ->method('render')
            ->with('factory-registry.twig', ['classDefinitionMap' => $this->classDefinitionMapMock])
            ->willReturn('<?php');

        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('exists')
            ->willReturn(false);

        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('mkdir')
            ->with($this->targetDirectory, 0775);

        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('writeToFile')
            ->with($this->targetDirectory . 'factory-registry.php', '<?php');

        $this->assertEquals(
            $this->factoryRegistryGenerator,
            $this->factoryRegistryGenerator->generate($this->classDefinitionMapMock)
        );
    }
}
