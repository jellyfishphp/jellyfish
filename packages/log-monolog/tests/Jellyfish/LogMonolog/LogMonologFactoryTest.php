<?php

declare(strict_types=1);

namespace Jellyfish\LogMonolog;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Log\LogConstants;
use Monolog\Logger;

class LogMonologFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @var \Jellyfish\LogMonolog\LogMonologFactory
     */
    protected LogMonologFactory $logMonologFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logMonologFactory = new LogMonologFactory(
            $this->configFacadeMock,
            DIRECTORY_SEPARATOR
        );
    }

    /**
     * @return void
     */
    public function testGetLogger(): void
    {
        $this->configFacadeMock->expects(static::atLeastOnce())
            ->method('get')
            ->withConsecutive(
                [LogConstants::LOGGER_NAME, LogConstants::DEFAULT_LOGGER_NAME],
                [LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL],
                [LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL]
            )->willReturnOnConsecutiveCalls(
                LogConstants::DEFAULT_LOGGER_NAME,
                LogConstants::DEFAULT_LOG_LEVEL,
                LogConstants::DEFAULT_LOG_LEVEL
            );

        static::assertInstanceOf(
            Logger::class,
            $this->logMonologFactory->getLogger()
        );
    }
}
