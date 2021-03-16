<?php

declare(strict_types=1);

namespace Jellyfish\Codeception\Lib;

use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Jellyfish\FilesystemSymfony\FilesystemSymfonyFacade;
use Jellyfish\FilesystemSymfony\FilesystemSymfonyFactory;
use Jellyfish\Finder\FinderFacadeInterface;
use Jellyfish\FinderSymfony\FinderSymfonyFacade;
use Jellyfish\FinderSymfony\FinderSymfonyFactory;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Jellyfish\SerializerSymfony\SerializerSymfonyFacade;
use Jellyfish\SerializerSymfony\SerializerSymfonyFactory;
use Jellyfish\Transfer\TransferFacade;
use Jellyfish\Transfer\TransferFacadeInterface;
use Jellyfish\Transfer\TransferFactory;

class TransferFacadeFactory
{
    /**
     * @return \Jellyfish\Transfer\TransferFacadeInterface
     */
    public function create(): TransferFacadeInterface
    {
        $transferFactory = new TransferFactory(
            $this->createFilesystemFacade(),
            $this->createSerializerFacade(),
            $this->createFinderFacade(),
            codecept_root_dir()
        );

        return new TransferFacade($transferFactory);
    }

    /**
     * @return \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected function createSerializerFacade(): SerializerFacadeInterface
    {
        $serializerSymfonyFactory = new SerializerSymfonyFactory();

        return new SerializerSymfonyFacade($serializerSymfonyFactory);
    }

    /**
     * @return \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    protected function createFilesystemFacade(): FilesystemFacadeInterface
    {
        $filesystemSymfonyFactory = new FilesystemSymfonyFactory();

        return new FilesystemSymfonyFacade($filesystemSymfonyFactory);
    }

    /**
     * @return \Jellyfish\Finder\FinderFacadeInterface
     */
    protected function createFinderFacade(): FinderFacadeInterface
    {
        $finderSymfonyFactory = new FinderSymfonyFactory();

        return new FinderSymfonyFacade($finderSymfonyFactory);
    }
}
