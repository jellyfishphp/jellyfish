<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

use Codeception\Test\Unit;

class FilesystemSymfonyFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\FilesystemSymfony\FilesystemSymfonyFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filesystemSymfonyFactoryMock;

    /**
     * @var \Jellyfish\FilesystemSymfony\FilesystemInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Jellyfish\FilesystemSymfony\FilesystemSymfonyFacade
     */
    protected FilesystemSymfonyFacade $filesystemSymfonyFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->filesystemSymfonyFactoryMock = $this->getMockBuilder(FilesystemSymfonyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystemMock = $this->getMockBuilder(FilesystemInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystemSymfonyFacade = new FilesystemSymfonyFacade($this->filesystemSymfonyFactoryMock);
    }

    /**
     * @return void
     */
    public function testMkdir(): void
    {
        $pathToCreate = '/path/to/generate';

        $this->filesystemSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getFilesystem')
            ->willReturn($this->filesystemMock);

        $this->filesystemMock->expects(static::atLeastOnce())
            ->method('mkdir')
            ->with($pathToCreate)
            ->willReturn($this->filesystemMock);

        static::assertEquals(
            $this->filesystemSymfonyFacade,
            $this->filesystemSymfonyFacade->mkdir($pathToCreate)
        );
    }

    /**
     * @return void
     */
    public function testRemove(): void
    {
        $fileToRemove = '/path/to/file.ext';

        $this->filesystemSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getFilesystem')
            ->willReturn($this->filesystemMock);

        $this->filesystemMock->expects(static::atLeastOnce())
            ->method('remove')
            ->with($fileToRemove)
            ->willReturn($this->filesystemMock);

        static::assertEquals(
            $this->filesystemSymfonyFacade,
            $this->filesystemSymfonyFacade->remove($fileToRemove)
        );
    }

    /**
     * @return void
     */
    public function testExists(): void
    {
        $pathToExistingFile = '/path/to/existing-file.ext';

        $this->filesystemSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getFilesystem')
            ->willReturn($this->filesystemMock);

        $this->filesystemMock->expects(static::atLeastOnce())
            ->method('exists')
            ->with($pathToExistingFile)
            ->willReturn(true);

        static::assertTrue(
            $this->filesystemSymfonyFacade->exists($pathToExistingFile)
        );
    }

    /**
     * @return void
     */
    public function testAppendToFile(): void
    {
        $pathToFile = '/path/to/file.ext';
        $content = 'dolor sit';

        $this->filesystemSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getFilesystem')
            ->willReturn($this->filesystemMock);

        $this->filesystemMock->expects(static::atLeastOnce())
            ->method('appendToFile')
            ->with($pathToFile, $content)
            ->willReturn($this->filesystemMock);

        static::assertEquals(
            $this->filesystemSymfonyFacade,
            $this->filesystemSymfonyFacade->appendToFile($pathToFile, $content)
        );
    }

    /**
     * @return void
     */
    public function testWriteToFile(): void
    {
        $pathToFile = '/path/to/file.ext';
        $content = 'dolor sit';

        $this->filesystemSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getFilesystem')
            ->willReturn($this->filesystemMock);

        $this->filesystemMock->expects(static::atLeastOnce())
            ->method('writeToFile')
            ->with($pathToFile, $content)
            ->willReturn($this->filesystemMock);

        static::assertEquals(
            $this->filesystemSymfonyFacade,
            $this->filesystemSymfonyFacade->writeToFile($pathToFile, $content)
        );
    }

    /**
     * @return void
     */
    public function testReadFromFile(): void
    {
        $pathToFile = '/path/to/file.ext';
        $content = 'dolor sit';

        $this->filesystemSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getFilesystem')
            ->willReturn($this->filesystemMock);

        $this->filesystemMock->expects(static::atLeastOnce())
            ->method('readFromFile')
            ->with($pathToFile)
            ->willReturn($content);

        static::assertEquals(
            $content,
            $this->filesystemSymfonyFacade->readFromFile($pathToFile)
        );
    }
}
