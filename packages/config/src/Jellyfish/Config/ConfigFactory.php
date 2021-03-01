<?php

declare(strict_types=1);

namespace Jellyfish\Config;

class ConfigFactory
{
    /**
     * @var string
     */
    protected $appDir;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var \Jellyfish\Config\ConfigInterface
     */
    protected $config;

    /**
     * @param string $appDir
     * @param string $environment
     */
    public function __construct(
        string $appDir,
        string $environment
    ) {
        $this->appDir = $appDir;
        $this->environment = $environment;
    }

    /**
     * @return \Jellyfish\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        if ($this->config === null) {
            $this->config = new Config($this->appDir, $this->environment);
        }

        return $this->config;
    }
}
