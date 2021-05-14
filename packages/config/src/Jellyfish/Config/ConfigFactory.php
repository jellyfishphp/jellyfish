<?php

declare(strict_types=1);

namespace Jellyfish\Config;

class ConfigFactory
{
    /**
     * @var string
     */
    protected string $appDir;

    /**
     * @var string
     */
    protected string $environment;

    /**
     * @var \Jellyfish\Config\ConfigInterface|null
     */
    protected ?ConfigInterface $config = null;

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
