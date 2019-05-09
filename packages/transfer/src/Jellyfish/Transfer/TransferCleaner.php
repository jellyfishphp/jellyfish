<?php

namespace Jellyfish\Transfer;

use Jellyfish\Filesystem\FilesystemInterface;
use Jellyfish\Finder\FinderFactoryInterface;
use SplFileInfo;

class TransferCleaner implements TransferCleanerInterface
{
    /**
     * @var string
     */
    protected $targetDirectory;

    /**
     * @var \Jellyfish\Filesystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var \Jellyfish\Finder\FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @param \Jellyfish\Finder\FinderFactoryInterface $finderFactory
     * @param \Jellyfish\Filesystem\FilesystemInterface $filesystem
     * @param string $targetDirectory
     */
    public function __construct(
        FinderFactoryInterface $finderFactory,
        FilesystemInterface $filesystem,
        string $targetDirectory
    ) {
        $this->finderFactory = $finderFactory;
        $this->targetDirectory = $targetDirectory;
        $this->filesystem = $filesystem;
    }

    /**
     * @return \Jellyfish\Transfer\TransferCleanerInterface
     */
    public function clean(): TransferCleanerInterface
    {
        return $this->cleanDirectory($this->targetDirectory);
    }

    /**
     * @param string $directory
     *
     * @return \Jellyfish\Transfer\TransferCleanerInterface
     */
    protected function cleanDirectory(string $directory): TransferCleanerInterface
    {
        $finder = $this->finderFactory->create();

        $iterator = $finder->in($directory)->getIterator();

        foreach ($iterator as $item) {
            if (!($item instanceof SplFileInfo)) {
                continue;
            }

            if ($item->isDir()) {
                $this->cleanDirectory($item->getRealPath());
            }

            $this->filesystem->remove($item->getRealPath());
        }

        return $this;
    }
}
