<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;
use Iterator;
use Jellyfish\Filesystem\FilesystemFacadeInterface;
use SplFileInfo;
use stdClass;

class ClassDefinitionMapLoaderTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapLoader
     */
    protected ClassDefinitionMapLoader $classDefinitionMapLoader;

    /**
     * @var \Jellyfish\Transfer\Definition\DefinitionFinderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $definitionFinderMock;

    /**
     * @var \Jellyfish\Filesystem\FilesystemFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filesystemFacadeMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $classDefinitionMapMapperMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapMergerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $classDefinitionMapMergerMock;

    /**
     * @var \Iterator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $iteratorMock;

    /**
     * @var \stdClass|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $stdClassMock;

    /**
     * @var \SplFileInfo|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $splFileInfoMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected array $classDefinitionMapMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->definitionFinderMock = $this->getMockBuilder(DefinitionFinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystemFacadeMock = $this->getMockBuilder(FilesystemFacadeInterface::class)
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
                ->getMock()
        ];

        $this->classDefinitionMapLoader = new ClassDefinitionMapLoader(
            $this->definitionFinderMock,
            $this->filesystemFacadeMock,
            $this->classDefinitionMapMapperMock,
            $this->classDefinitionMapMergerMock
        );
    }

    /**
     * @return void
     */
    public function testLoad(): void
    {
        $realPath = codecept_data_dir('test.transfer.json');

        $this->definitionFinderMock->expects(static::atLeastOnce())
            ->method('find')
            ->willReturn($this->iteratorMock);

        $this->iteratorMock->expects(static::once())
            ->method('rewind');

        $this->iteratorMock->expects(static::exactly(2))
            ->method('next');

        $this->iteratorMock->expects(static::exactly(3))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $this->iteratorMock->expects(static::exactly(2))
            ->method('current')
            ->willReturnOnConsecutiveCalls($this->splFileInfoMock, $this->stdClassMock);

        $this->splFileInfoMock->expects(static::atLeastOnce())
            ->method('getRealPath')
            ->willReturn($realPath);

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('readFromFile')
            ->with($realPath)
            ->willReturn(\file_get_contents($realPath));

        $this->classDefinitionMapMapperMock->expects(static::atLeastOnce())
            ->method('from')
            ->willReturn($this->classDefinitionMapMock);

        $this->classDefinitionMapMergerMock->expects(static::atLeastOnce())
            ->method('merge')
            ->with([], $this->classDefinitionMapMock)
            ->willReturn($this->classDefinitionMapMock);

        $classDefinitionMap = $this->classDefinitionMapLoader->load();

        static::assertEquals($this->classDefinitionMapMock, $classDefinitionMap);
    }
}
