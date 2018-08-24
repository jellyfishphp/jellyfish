<?php

namespace Jellyfish\Config;

interface ConfigInterface
{
    /**
     * @param string $key
     * @param $default
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasValue(string $key): bool;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasKey(string $key): bool;
}
