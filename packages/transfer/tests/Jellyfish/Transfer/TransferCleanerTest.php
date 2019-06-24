<?php

namespace Jellyfish\Transfer;

use Codeception\Test\Unit;
use Iterator;
use Jellyfish\Filesystem\FilesystemInterface;
use Jellyfish\Finder\FinderFactoryInterface;
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
     * @var \Jellyfish\Filesystem\FilesystemInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Jellyfish\Finder\FinderFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $finderFactoryMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->filesystemMock = $this->getMockBuilder(FilesystemInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderFactoryMock = $this->getMockBuilder(FinderFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->targetDirectory = '/root/src/Generated/Transfer/';

        $this->transferCleaner = new TransferCleaner(
            $this->finderFactoryMock,
            $this->filesystemMock,
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

        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('exists')
            ->with($this->targetDirectory)
            ->willReturn(true);

        $this->finderFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturnOnConsecutiveCalls($finderMocks[0], $finderMocks[1]);

        $finderMocks[0]->expects($this->atLeastOnce())
            ->method('in')
            ->with($this->targetDirectory)
            ->willReturn($finderMocks[0]);

        $finderMocks[0]->expects($this->atLeastOnce())
            ->method('depth')
            ->with(0)
            ->willReturn($finderMocks[0]);

        $finderMocks[0]->expects($this->atLeastOnce())
            ->method('getIterator')
            ->willReturn($iteratorMocks[0]);

        $iteratorMocks[0]->expects($this->atLeastOnce())
            ->method('rewind');

        $iteratorMocks[0]->expects($this->atLeastOnce())
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $iteratorMocks[0]->expects($this->atLeastOnce())
            ->method('current')
            ->willReturnOnConsecutiveCalls($splFileInfoMocks[0], $splFileInfoMocks[1]);

        $splFileInfoMocks[0]->expects($this->atLeastOnce())
            ->method('isDir')
            ->willReturn(true);

        $splFileInfoMocks[0]->expects($this->atLeastOnce())
            ->method('getRealPath')
            ->willReturn($this->targetDirectory . 'Product');

        $splFileInfoMocks[1]->expects($this->atLeastOnce())
            ->method('getRealPath')
            ->willReturn($this->targetDirectory . 'factory-registry.php');

        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('remove')
            ->withConsecutive(
                [$this->targetDirectory . 'Product/AttributeTransfer.php'],
                [$this->targetDirectory . 'Product']
            )->willReturn($this->filesystemMock);

        $finderMocks[1]->expects($this->atLeastOnce())
            ->method('in')
            ->with($this->targetDirectory . 'Product')
            ->willReturn($finderMocks[1]);

        $finderMocks[1]->expects($this->atLeastOnce())
            ->method('depth')
            ->with(0)
            ->willReturn($finderMocks[1]);

        $finderMocks[1]->expects($this->atLeastOnce())
            ->method('getIterator')
            ->willReturn($iteratorMocks[1]);

        $iteratorMocks[1]->expects($this->atLeastOnce())
            ->method('rewind');

        $iteratorMocks[1]->expects($this->atLeastOnce())
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);

        $iteratorMocks[1]->expects($this->atLeastOnce())
            ->method('current')
            ->willReturn($splFileInfoMocks[2]);

        $splFileInfoMocks[2]->expects($this->atLeastOnce())
            ->method('isDir')
            ->willReturn(false);

        $splFileInfoMocks[2]->expects($this->atLeastOnce())
            ->method('getRealPath')
            ->willReturn($this->targetDirectory . 'Product/AttributeTransfer.php');

        $this->assertEquals($this->transferCleaner, $this->transferCleaner->clean());
    }

    /**
     * @return void
     */
    public function testCleanWithInvalidIteratorElement(): void
    {
        $this->filesystemMock->expects($this->atLeastOnce())
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

        $this->finderFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($finderMock);

        $finderMock->expects($this->atLeastOnce())
            ->method('in')
            ->with($this->targetDirectory)
            ->willReturn($finderMock);

        $finderMock->expects($this->atLeastOnce())
            ->method('depth')
            ->with(0)
            ->willReturn($finderMock);

        $finderMock->expects($this->atLeastOnce())
            ->method('getIterator')
            ->willReturn($iteratorMock);

        $iteratorMock->expects($this->atLeastOnce())
            ->method('rewind');

        $iteratorMock->expects($this->atLeastOnce())
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);

        $iteratorMock->expects($this->atLeastOnce())
            ->method('current')
            ->willReturn($stdClassMock);

        $this->filesystemMock->expects($this->never())
            ->method('remove')
            ->willReturn($this->filesystemMock);
        
        $this->assertEquals($this->transferCleaner, $this->transferCleaner->clean());
    }

    /**
     * @return void
     */
    public function testCleanWithNonExistingTargetDirectory(): void
    {
        $this->filesystemMock->expects($this->atLeastOnce())
            ->method('exists')
            ->with($this->targetDirectory)
            ->willReturn(false);

        $this->finderFactoryMock->expects($this->never())
            ->method('create');

        $this->assertEquals($this->transferCleaner, $this->transferCleaner->clean());
    }
}
