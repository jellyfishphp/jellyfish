<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

interface TransferCleanerInterface
{
    /**
     * @return \Jellyfish\Transfer\TransferCleanerInterface
     */
    public function clean(): TransferCleanerInterface;
}
