<?php

namespace Jellyfish\ActivityMonitor;

class Activity implements ActivityInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return \Jellyfish\ActivityMonitor\ActivityInterface
     */
    public function setId(int $id): ActivityInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Jellyfish\ActivityMonitor\ActivityInterface
     */
    public function setName(string $name): ActivityInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return \Jellyfish\ActivityMonitor\ActivityInterface
     */
    public function setStatus(string $status): ActivityInterface
    {
        $this->status = $status;

        return $this;
    }
}
