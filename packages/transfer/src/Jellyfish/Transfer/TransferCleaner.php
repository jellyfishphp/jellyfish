<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Jellyfish\Filesystem\FilesystemInterface;
use Jellyfish\Finder\FinderFactoryInterface;
use SplFileInfo;

/**
 * @see \Jellyfish\Transfer\TransferCleanerTest
 */
class TransferCleaner implements TransferCleanerInterface
{
    protected const EXCLUDED_FILE = 'factory-registry.php';

    protected string $targetDirectory;

    protected FilesystemInterface $filesystem;

    protected FinderFactoryInterface $finderFactory;

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
        if (!$this->canClean()) {
            return $this;
        }

        return $this->cleanDirectory($this->targetDirectory);
    }

    /**
     * @return bool
     */
    protected function canClean(): bool
    {
        return $this->filesystem->exists($this->targetDirectory);
    }

    /**
     * @param string $directory
     *
     * @return \Jellyfish\Transfer\TransferCleanerInterface
     */
    protected function cleanDirectory(string $directory): TransferCleanerInterface
    {
        $finder = $this->finderFactory->create();

        $iterator = $finder->in($directory)->depth(0)->getIterator();

        foreach ($iterator as $item) {
            if (!($item instanceof SplFileInfo)) {
                continue;
            }

            if (!is_string($item->getRealPath())) {
                continue;
            }

            $itemRealPath = $item->getRealPath();

            if (!$this->canRemove($itemRealPath)) {
                continue;
            }

            if ($item->isDir()) {
                $this->cleanDirectory($itemRealPath);
            }

            $this->filesystem->remove($itemRealPath);
        }

        return $this;
    }

    /**
     * @param string $realPathOfItem
     *
     * @return bool
     */
    protected function canRemove(string $realPathOfItem): bool
    {
        $realPathOfFactoryRegistry = $this->targetDirectory . static::EXCLUDED_FILE;

        return $realPathOfFactoryRegistry !== $realPathOfItem;
    }
}
