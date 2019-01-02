<?php

namespace Jellyfish\Event\Fixtures;

class Payload
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return \Jellyfish\Event\Fixtures\Payload
     */
    public function setName(string $name): Payload
    {
        $this->name = $name;

        return $this;
    }
}
