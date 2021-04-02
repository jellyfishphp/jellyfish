<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Generated\Transfer\ActivityMonitor\Activity;

interface ActivityReaderInterface
{
    /**
     * @param int $id
     *
     * @return \Generated\Transfer\ActivityMonitor\Activity|null
     */
    public function getById(int $id): ?Activity;

    /**
     * @return \Generated\Transfer\ActivityMonitor\Activity[]
     */
    public function getAll(): array;
}
