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
     *
     * @throws \Jellyfish\Process\Exception\AlreadyStartedException
     * @throws \Jellyfish\Process\Exception\NotStartedException
     * @throws \Jellyfish\Process\Exception\NotTerminatedException
     */
    public function getById(int $id): ?Activity;

    /**
     * @return \Generated\Transfer\ActivityMonitor\Activity[]
     *
     * @throws \Jellyfish\Process\Exception\AlreadyStartedException
     * @throws \Jellyfish\Process\Exception\NotStartedException
     * @throws \Jellyfish\Process\Exception\NotTerminatedException
     */
    public function getAll(): array;
}
