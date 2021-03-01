<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Codeception\Test\Unit;
use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Jellyfish\Finder\FinderFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class TransferFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Filesystem\FilesystemFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filesystemFacadeMock;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */

    protected $serializerFacadeMock;

    /**
     * @var \Jellyfish\Finder\FinderFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $finderFacadeMock;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var \Jellyfish\Transfer\TransferFactory
     */
    protected $transferFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->filesystemFacadeMock = $this->getMockBuilder(FilesystemFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderFacadeMock = $this->getMockBuilder(FinderFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->rootDir = DIRECTORY_SEPARATOR;

        $this->transferFactory = new TransferFactory(
            $this->filesystemFacadeMock,
            $this->serializerFacadeMock,
            $this->finderFacadeMock,
            $this->rootDir
        );
    }

    /**
     * @return void
     */
    public function testGetTransferGenerator(): void
    {
        static::assertInstanceOf(
            TransferGenerator::class,
            $this->transferFactory->getTransferGenerator()
        );
    }

    /**
     * @return void
     */
    public function testGetTransferCleaner(): void
    {
        static::assertInstanceOf(
            TransferCleaner::class,
            $this->transferFactory->getTransferCleaner()
        );
    }
}
