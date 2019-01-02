<?php

namespace Jellyfish\Config;

interface ConfigInterface
{
    /**
     * @param string $key
     * @param string|null $default
     *
     * @return string
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     */
    public function get(string $key, ?string $default = null): string;
}
