<?php

namespace Jellyfish\Config;

interface ConfigFacadeInterface
{
    /**
     * @param string $key
     * @param string|null $default
     *
     * @return string
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    public function get(string $key, ?string $default = null): string;
}
