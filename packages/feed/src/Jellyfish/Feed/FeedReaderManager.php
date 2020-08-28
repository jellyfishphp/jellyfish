<?php

declare(strict_types=1);

namespace Jellyfish\Feed;

use Jellyfish\Feed\Exception\FeedReaderNotFoundException;

use function array_key_exists;
use function sprintf;

class FeedReaderManager implements FeedReaderManagerInterface
{
    /**
     * @var \Jellyfish\Feed\FeedReaderInterface[]
     */
    protected $feedReaders;

    public function __construct()
    {
        $this->feedReaders = [];
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function existsFeedReader(string $identifier): bool
    {
        return array_key_exists($identifier, $this->feedReaders);
    }

    /**
     * @param string $identifier
     * @param \Jellyfish\Feed\FeedReaderInterface $feedReader
     *
     * @return \Jellyfish\Feed\FeedReaderManagerInterface
     */
    public function setFeedReader(string $identifier, FeedReaderInterface $feedReader): FeedReaderManagerInterface
    {
        $this->feedReaders[$identifier] = $feedReader;

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return \Jellyfish\Feed\FeedReaderManagerInterface
     *
     * @throws \Jellyfish\Feed\Exception\FeedReaderNotFoundException
     */
    public function unsetFeedReader(string $identifier): FeedReaderManagerInterface
    {
        if (!$this->existsFeedReader($identifier)) {
            throw new FeedReaderNotFoundException(
                sprintf('Feed reader with identifier "%s" not found.', $identifier)
            );
        }

        unset($this->feedReaders[$identifier]);

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return \Jellyfish\Feed\FeedReaderInterface
     *
     * @throws \Jellyfish\Feed\Exception\FeedReaderNotFoundException
     */
    public function getFeederReader(string $identifier): FeedReaderInterface
    {
        if (!$this->existsFeedReader($identifier)) {
            throw new FeedReaderNotFoundException(
                sprintf('Feed reader with identifier "%s" not found.', $identifier)
            );
        }

        return $this->feedReaders[$identifier];
    }

    /**
     * @param string $identifier
     *
     * @return string
     *
     * @throws \Jellyfish\Feed\Exception\FeedReaderNotFoundException
     */
    public function readFromFeedReader(string $identifier): string
    {
        return $this->getFeederReader($identifier)->read();
    }
}
