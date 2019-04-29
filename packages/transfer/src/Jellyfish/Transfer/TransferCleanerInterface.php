<?php

namespace Jellyfish\Transfer;

interface TransferCleanerInterface
{
    /**
     * @return \Jellyfish\Transfer\TransferCleanerInterface
     */
    public function clean(): TransferCleanerInterface;
}
