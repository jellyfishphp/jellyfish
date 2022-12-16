<?php

declare(strict_types=1);

namespace Jellyfish\LogRoadRunner;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Log\LogConstants;

class LogRoadRunnerFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @var \Jellyfish\LogRoadRunner\LogRoadRunnerFactory
     */
    protected LogRoadRunnerFactory $logRoadRunnerFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logRoadRunnerFactory = new LogRoadRunnerFactory(
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
            ->with(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL)
            ->willReturn(LogConstants::DEFAULT_LOG_LEVEL,);

        static::assertInstanceOf(
            Logger::class,
            $this->logRoadRunnerFactory->getLogger()
        );
    }
}
