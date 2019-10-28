<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

interface ClassDefinitionMapMapperInterface
{
    /**
     * @param string $data
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]
     */
    public function from(string $data): array;
}
