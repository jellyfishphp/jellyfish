<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Iterator;

interface DefinitionFinderInterface
{
    /**
     * @return \Iterator
     */
    public function find(): Iterator;
}
