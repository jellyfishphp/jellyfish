<?php

declare(strict_types=1);

namespace Jellyfish\Console;

use ArrayObject;

class ConsoleFactory
{
    /**
     * @var \ArrayObject|null
     */
    protected ?ArrayObject $commandList = null;

    /**
     * @return \ArrayObject
     */
    public function getCommandList(): ArrayObject
    {
        if ($this->commandList === null) {
            $this->commandList = new ArrayObject();
        }

        return $this->commandList;
    }
}
