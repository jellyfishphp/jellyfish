<?php

declare(strict_types = 1);

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;
use Iterator;
use Jellyfish\Filesystem\FilesystemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use SplFileInfo;
use stdClass;

class ClassDefinitionMapLoaderTest extends Unit
{
    protected MockObject&DefinitionFinderInterface $definitionFinderMock;

    protected FilesystemInterface&MockObject $filesystemMock;

    protected MockObject&ClassDefinitionMapMapperInterface $classDefinitionMapMapperMock;

    protected MockObject&ClassDefinitionMapMergerInterface $classDefinitionMapMergerMock;

    protected MockObject&Iterator $iteratorMock;

    protected MockObject&stdClass $stdClassMock;

    protected SplFileInfo&MockObject $splFileInfoMock;

    /**
     * @var array<\Jellyfish\Transfer\Definition\ClassDefinitionInterface&\PHPUnit\Framework\MockObject\MockObject>
     */
    protected array $classDefinitionMapMock;

    protected ClassDefinitionMapLoader $classDefinitionMapLoader;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->definitionFinderMock = $this->getMockBuilder(DefinitionFinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystemMock = $this->getMockBuilder(FilesystemInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classDefinitionMapMapperMock = $this->getMockBuilder(ClassDefinitionMapMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classDefinitionMapMergerMock = $this->getMockBuilder(ClassDefinitionMapMergerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->iteratorMock = $this->getMockBuilder(Iterator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stdClassMock = $this->getMockBuilder(stdClass::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->splFileInfoMock = $this->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classDefinitionMapMock = [
            $this->getMockBuilder(ClassDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->classDefinitionMapLoader = new ClassDefinitionMapLoader(
            $this->definitionFinderMock,
            $this->filesystemMock,
            $this->classDefinitionMapMapperMock,
            $this->classDefinitionMapMergerMock,
        );
    }

    /**
     * @return void
     */
    public function testLoad(): void
    {
        $realPath = \codecept_data_dir('test.transfer.json');

        $this->definitionFinderMock->expects($this->atLeastOnce())
            ->method('find')
            ->willReturn($this->iteratorMock);

        $this->iteratorMock->expects($this->once())
            ->method('rewind');

        $this->iteratorMock->expects($this->exactly(2))
            ->method('next');

        $this->iteratorMock->expects($this->exactly(3))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $this->iteratorMock->expects($this->exactly(2))
            ->method('current')
            ->willReturnOnConsecutiveCalls($this->splFileInfoMock, $this->stdClassMock);

        $this->splFileInfoMock->expects($this->atLeastOnce())
            ->method('getRealPath')
            ->willReturn($realPath);

        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('readFromFile')
            ->with($realPath)
            ->willReturn(\file_get_contents($realPath));

        $this->classDefinitionMapMapperMock->expects($this->atLeastOnce())
            ->method('from')
            ->willReturn($this->classDefinitionMapMock);

        $this->classDefinitionMapMergerMock->expects($this->atLeastOnce())
            ->method('merge')
            ->with([], $this->classDefinitionMapMock)
            ->willReturn($this->classDefinitionMapMock);

        $classDefinitionMap = $this->classDefinitionMapLoader->load();

        $this->assertEquals($this->classDefinitionMapMock, $classDefinitionMap);
    }
}
