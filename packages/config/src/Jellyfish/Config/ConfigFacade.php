<?php

declare(strict_types=1);

namespace Jellyfish\Config;

class ConfigFacade implements ConfigFacadeInterface
{
    /**
     * @var \Jellyfish\Config\ConfigFactory
     */
    protected ConfigFactory $factory;

    /**
     * @param \Jellyfish\Config\ConfigFactory $factory
     */
    public function __construct(ConfigFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $key
     * @param string|null $default
     *
     * @return string
     */
    public function get(string $key, ?string $default = null): string
    {
        return $this->factory->getConfig()->get($key, $default);
    }
}
