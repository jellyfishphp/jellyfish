<?php

declare(strict_types=1);

namespace Jellyfish\Config;

use Codeception\Test\Unit;

class ConfigFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFactoryMock;
    /**
     * @var \Jellyfish\Config\ConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configMock;

    /**
     * @var \Jellyfish\Config\ConfigFacade
     */
    protected $configFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configFactoryMock = $this->getMockBuilder(ConfigFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configFacade = new ConfigFacade($this->configFactoryMock);
    }

    /**
     * @return void
     */
    public function testGet(): void
    {
        $key = 'foo';
        $value = 'bar';

        $this->configFactoryMock->expects(static::atLeastOnce())
            ->method('getConfig')
            ->willReturn($this->configMock);

        $this->configMock->expects(static::atLeastOnce())
            ->method('get')
            ->with($key)
            ->willReturn($value);

        static::assertEquals(
            $value,
            $this->configFacade->get('foo')
        );
    }
}
