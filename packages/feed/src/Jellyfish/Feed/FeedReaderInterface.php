<?php

declare(strict_types=1);

namespace Jellyfish\Feed;

interface FeedReaderInterface
{
    /**
     * @return string
     */
    public function read(): string;
}
