<?php

namespace Jellyfish\Transfer;

interface TransferGeneratorInterface
{
    /**
     * @return \Jellyfish\Transfer\TransferGeneratorInterface
     */
    public function generate(): TransferGeneratorInterface;
}
