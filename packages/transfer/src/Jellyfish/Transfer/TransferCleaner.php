<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Jellyfish\Finder\FinderFacadeInterface;
use SplFileInfo;

use function is_string;

class TransferCleaner implements TransferCleanerInterface
{
    /**
     * @var string
     */
    protected $targetDirectory;

    /**
     * @var \Jellyfish\Filesystem\FilesystemFacadeInterface
     */
    protected $filesystemFacade;

    /**
     * @var \Jellyfish\Finder\FinderFacadeInterface
     */
    protected $finderFacade;

    /**
     * @param \Jellyfish\Finder\FinderFacadeInterface $finderFacade
     * @param \Jellyfish\Filesystem\FilesystemFacadeInterface $filesystemFacade
     * @param string $targetDirectory
     */
    public function __construct(
        FinderFacadeInterface $finderFacade,
        FilesystemFacadeInterface $filesystemFacade,
        string $targetDirectory
    ) {
        $this->finderFacade = $finderFacade;
        $this->targetDirectory = $targetDirectory;
        $this->filesystemFacade = $filesystemFacade;
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
        return $this->filesystemFacade->exists($this->targetDirectory);
    }

    /**
     * @param string $directory
     *
     * @return \Jellyfish\Transfer\TransferCleanerInterface
     */
    protected function cleanDirectory(string $directory): TransferCleanerInterface
    {
        $finder = $this->finderFacade->createFinder();

        $iterator = $finder->in([$directory])
            ->depth(0)
            ->getIterator();

        foreach ($iterator as $item) {
            if (!($item instanceof SplFileInfo) || !is_string($item->getRealPath())) {
                continue;
            }

            $itemRealPath = $item->getRealPath();

            if ($item->isDir()) {
                $this->cleanDirectory($itemRealPath);
            }

            $this->filesystemFacade->remove($itemRealPath);
        }

        return $this;
    }
}
