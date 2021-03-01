<?php

declare(strict_types=1);

namespace Jellyfish\Config;

use Codeception\Test\Unit;

class ConfigFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFactory
     */
    protected $configFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configFactory = new ConfigFactory('/app', 'testing');
    }

    /**
     * @return void
     */
    public function testGetConfig(): void
    {
        static::assertInstanceOf(Config::class, $this->configFactory->getConfig());
    }
}
