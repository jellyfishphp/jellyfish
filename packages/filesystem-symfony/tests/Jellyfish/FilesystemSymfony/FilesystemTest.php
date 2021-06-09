<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Filesystem\Exception\IOException;
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
    protected Filesystem $filesystem;

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

        $this->symfonyFilesystemMock->expects(static::atLeastOnce())
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

        $this->symfonyFilesystemMock->expects(static::atLeastOnce())
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

        $this->symfonyFilesystemMock->expects(static::atLeastOnce())
            ->method('exists')
            ->with($pathToExistingFile)
            ->willReturn(true);


        static::assertTrue($this->filesystem->exists($pathToExistingFile));
    }

    /**
     * @return void
     */
    public function testAppendToFile(): void
    {
        $currentContent = 'Lorem ipsum ';
        $root = vfsStream::setup('root', null, ['to' => ['file.ext' => $currentContent]]);
        $pathToFile = $root->url() . '/to/file.ext';
        $content = 'dolor sit';

        $this->filesystem->appendToFile($pathToFile, $content);

        static::assertFileExists($pathToFile);
        static::assertEquals($currentContent . $content, \file_get_contents($pathToFile));
    }

    /**
     * @return void
     */
    public function testAppendToFileWithNonExistingSubdirectory(): void
    {
        $root = vfsStream::setup();
        $pathToFile = $root->url() . '/to/file.ext';
        $content = 'dolor sit';

        try {
            $this->filesystem->appendToFile($pathToFile, $content);
            static::fail();
        } catch (IOException $e) {
            static::assertFileNotExists($pathToFile);
        }
    }

    /**
     * @return void
     */
    public function testWriteToFileWithNonExistingSubdirectory(): void
    {
        $root = vfsStream::setup();
        $pathToFile = $root->url() . '/to/file.ext';
        $content = 'Lorem ipsum';

        try {
            $this->filesystem->writeToFile($pathToFile, $content);
            static::fail();
        } catch (IOException $e) {
            static::assertFileNotExists($pathToFile);
        }
    }

    /**
     * @return void
     */
    public function testWriteToFile(): void
    {
        $root = vfsStream::setup('root', null, ['to' => []]);
        $pathToFile = $root->url() . '/to/file.ext';
        $content = 'Lorem ipsum';

        $this->filesystem->writeToFile($pathToFile, $content);

        static::assertFileExists($pathToFile);
        static::assertEquals($content, \file_get_contents($pathToFile));
    }

    /**
     * @return void
     */
    public function testReadFromFile(): void
    {
        $root = vfsStream::setup('root', null, ['to' => []]);
        $pathToNonExistingFile = $root->url() . '/to/file.ext';
        $content = 'Lorem ipsum';
        @\file_put_contents($pathToNonExistingFile, $content);

        static::assertEquals($content, $this->filesystem->readFromFile($pathToNonExistingFile));
    }

    /**
     * @return void
     */
    public function testReadFromNonExistingFile(): void
    {
        $root = vfsStream::setup('root', null, ['to' => []]);
        $pathToNonExistingFile = $root->url() . '/to/file.ext';

        try {
            $this->filesystem->readFromFile($pathToNonExistingFile);
            static::fail();
        } catch (IOException $e) {
        }
    }
}
