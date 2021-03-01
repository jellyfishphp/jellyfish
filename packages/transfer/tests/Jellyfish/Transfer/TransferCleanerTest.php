<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Codeception\Test\Unit;
use Iterator;
use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Jellyfish\Finder\FinderFacadeInterface;
use Jellyfish\Finder\FinderInterface;
use SplFileInfo;
use stdClass;

class TransferCleanerTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\TransferCleaner
     */
    protected $transferCleaner;

    /**
     * @var string
     */
    protected $targetDirectory;

    /**
     * @var \Jellyfish\Filesystem\FilesystemFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filesystemFacadeMock;

    /**
     * @var \Jellyfish\Finder\FinderFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $finderFacadeMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->filesystemFacadeMock = $this->getMockBuilder(FilesystemFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderFacadeMock = $this->getMockBuilder(FinderFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->targetDirectory = '/root/src/Generated/Transfer/';

        $this->transferCleaner = new TransferCleaner(
            $this->finderFacadeMock,
            $this->filesystemFacadeMock,
            $this->targetDirectory
        );
    }

    /**
     * @return void
     */
    public function testClean(): void
    {
        $finderMocks = [
            $this->getMockBuilder(FinderInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(FinderInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $iteratorMocks = [
            $this->getMockBuilder(Iterator::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(Iterator::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $splFileInfoMocks = [
            $this->getMockBuilder(SplFileInfo::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(SplFileInfo::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(SplFileInfo::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('exists')
            ->with($this->targetDirectory)
            ->willReturn(true);

        $this->finderFacadeMock->expects(static::atLeastOnce())
            ->method('createFinder')
            ->willReturnOnConsecutiveCalls($finderMocks[0], $finderMocks[1]);

        $finderMocks[0]->expects(static::atLeastOnce())
            ->method('in')
            ->with([$this->targetDirectory])
            ->willReturn($finderMocks[0]);

        $finderMocks[0]->expects(static::atLeastOnce())
            ->method('depth')
            ->with(0)
            ->willReturn($finderMocks[0]);

        $finderMocks[0]->expects(static::atLeastOnce())
            ->method('getIterator')
            ->willReturn($iteratorMocks[0]);

        $iteratorMocks[0]->expects(static::atLeastOnce())
            ->method('rewind');

        $iteratorMocks[0]->expects(static::atLeastOnce())
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $iteratorMocks[0]->expects(static::atLeastOnce())
            ->method('current')
            ->willReturnOnConsecutiveCalls($splFileInfoMocks[0], $splFileInfoMocks[1]);

        $splFileInfoMocks[0]->expects(static::atLeastOnce())
            ->method('isDir')
            ->willReturn(true);

        $splFileInfoMocks[0]->expects(static::atLeastOnce())
            ->method('getRealPath')
            ->willReturn($this->targetDirectory . 'Product');

        $splFileInfoMocks[1]->expects(static::atLeastOnce())
            ->method('getRealPath')
            ->willReturn($this->targetDirectory . 'factory-registry.php');

        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('remove')
            ->withConsecutive(
                [$this->targetDirectory . 'Product/AttributeTransfer.php'],
                [$this->targetDirectory . 'Product']
            )->willReturn($this->filesystemFacadeMock);

        $finderMocks[1]->expects(static::atLeastOnce())
            ->method('in')
            ->with([$this->targetDirectory . 'Product'])
            ->willReturn($finderMocks[1]);

        $finderMocks[1]->expects(static::atLeastOnce())
            ->method('depth')
            ->with(0)
            ->willReturn($finderMocks[1]);

        $finderMocks[1]->expects(static::atLeastOnce())
            ->method('getIterator')
            ->willReturn($iteratorMocks[1]);

        $iteratorMocks[1]->expects(static::atLeastOnce())
            ->method('rewind');

        $iteratorMocks[1]->expects(static::atLeastOnce())
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);

        $iteratorMocks[1]->expects(static::atLeastOnce())
            ->method('current')
            ->willReturn($splFileInfoMocks[2]);

        $splFileInfoMocks[2]->expects(static::atLeastOnce())
            ->method('isDir')
            ->willReturn(false);

        $splFileInfoMocks[2]->expects(static::atLeastOnce())
            ->method('getRealPath')
            ->willReturn($this->targetDirectory . 'Product/AttributeTransfer.php');

        static::assertEquals($this->transferCleaner, $this->transferCleaner->clean());
    }

    /**
     * @return void
     */
    public function testCleanWithInvalidIteratorElement(): void
    {
        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('exists')
            ->with($this->targetDirectory)
            ->willReturn(true);

        $finderMock = $this->getMockBuilder(FinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $iteratorMock = $this->getMockBuilder(Iterator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stdClassMock = $this->getMockBuilder(stdClass::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderFacadeMock->expects(static::atLeastOnce())
            ->method('createFinder')
            ->willReturn($finderMock);

        $finderMock->expects(static::atLeastOnce())
            ->method('in')
            ->with([$this->targetDirectory])
            ->willReturn($finderMock);

        $finderMock->expects(static::atLeastOnce())
            ->method('depth')
            ->with(0)
            ->willReturn($finderMock);

        $finderMock->expects(static::atLeastOnce())
            ->method('getIterator')
            ->willReturn($iteratorMock);

        $iteratorMock->expects(static::atLeastOnce())
            ->method('rewind');

        $iteratorMock->expects(static::atLeastOnce())
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);

        $iteratorMock->expects(static::atLeastOnce())
            ->method('current')
            ->willReturn($stdClassMock);

        $this->filesystemFacadeMock->expects(static::never())
            ->method('remove')
            ->willReturn($this->filesystemFacadeMock);

        static::assertEquals($this->transferCleaner, $this->transferCleaner->clean());
    }

    /**
     * @return void
     */
    public function testCleanWithNonExistingTargetDirectory(): void
    {
        $this->filesystemFacadeMock->expects(static::atLeastOnce())
            ->method('exists')
            ->with($this->targetDirectory)
            ->willReturn(false);

        $this->finderFacadeMock->expects(static::never())
            ->method('createFinder');

        static::assertEquals($this->transferCleaner, $this->transferCleaner->clean());
    }
}
