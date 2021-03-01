<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Codeception\Test\Unit;
use Monolog\Logger;

class LogFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Log\LogFactory
     */
    protected $logFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->logFactory = new LogFactory();
    }

    /**
     * @return void
     */
    public function testGetLogger(): void
    {
        static::assertInstanceOf(
            Logger::class,
            $this->logFactory->getLogger()
        );
    }
}
