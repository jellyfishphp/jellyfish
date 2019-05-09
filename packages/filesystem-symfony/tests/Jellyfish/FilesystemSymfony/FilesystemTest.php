<?php

namespace Jellyfish\FilesystemSymfony;

use Codeception\Test\Unit;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class FilesystemTest extends Unit
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $symfonyFilesystemMock;

    /**
     * @var \Jellyfish\FilesystemSymfony\Filesystem
     */
    protected $filesystem;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonyFilesystemMock = $this->getMockBuilder(SymfonyFilesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystem = new Filesystem($this->symfonyFilesystemMock);
    }

    /**
     * @return void
     */
    public function testMkdir(): void
    {
        $pathToCreate = '/path/to/generate';

        $this->symfonyFilesystemMock->expects($this->atLeastOnce())
            ->method('mkdir')
            ->with($pathToCreate);


        $this->filesystem->mkdir($pathToCreate);
    }

    /**
     * @return void
     */
    public function testRemove(): void
    {
        $fileToRemove = '/path/to/file.ext';

        $this->symfonyFilesystemMock->expects($this->atLeastOnce())
            ->method('remove')
            ->with($fileToRemove);


        $this->filesystem->remove($fileToRemove);
    }

    /**
     * @return void
     */
    public function testExists(): void
    {
        $pathToExistingFile = '/path/to/existing-file.ext';

        $this->symfonyFilesystemMock->expects($this->atLeastOnce())
            ->method('exists')
            ->with($pathToExistingFile)
            ->willReturn(true);


        $this->assertTrue($this->filesystem->exists($pathToExistingFile));
    }

    /**
     * @return void
     */
    public function testAppendToFile(): void
    {
        $pathToFile = '/path/to/file.ext';
        $content = 'Lorem Ipsum';

        $this->symfonyFilesystemMock->expects($this->atLeastOnce())
            ->method('appendToFile')
            ->with($pathToFile, $content);


        $this->filesystem->appendToFile($pathToFile, $content);
    }

    /**
     * @return void
     */
    public function testWriteToFile(): void
    {
        $pathToFile = '/path/to/file.ext';
        $content = 'Lorem Ipsum';

        $this->symfonyFilesystemMock->expects($this->atLeastOnce())
            ->method('dumpFile')
            ->with($pathToFile, $content);


        $this->filesystem->writeToFile($pathToFile, $content);
    }
}
