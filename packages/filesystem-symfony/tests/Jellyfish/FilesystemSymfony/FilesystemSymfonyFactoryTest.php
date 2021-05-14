<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

use Codeception\Test\Unit;

class FilesystemSymfonyFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\FilesystemSymfony\FilesystemSymfonyFactory
     */
    protected FilesystemSymfonyFactory $filesystemSymfonyFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->filesystemSymfonyFactory = new FilesystemSymfonyFactory();
    }

    /**
     * @return void
     */
    public function testGetFilesystem(): void
    {
        static::assertInstanceOf(
            Filesystem::class,
            $this->filesystemSymfonyFactory->getFilesystem()
        );
    }
}
