<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

interface TransferGeneratorInterface
{
    /**
     * @return \Jellyfish\Transfer\TransferGeneratorInterface
     */
    public function generate(): TransferGeneratorInterface;
}
