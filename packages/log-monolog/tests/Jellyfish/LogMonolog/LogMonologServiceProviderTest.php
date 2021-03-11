<?php

declare(strict_types=1);

namespace Jellyfish\LogMonolog;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Log\LogConstants;
use Jellyfish\Log\LogFacadeInterface;
use Pimple\Container;

class LogMonologServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\ServiceProviderInterface
     */
    protected $logMonologServiceProvider;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container->offsetSet('root_dir', DIRECTORY_SEPARATOR);

        $this->container->offsetSet(ConfigConstants::FACADE, static function () use ($self) {
            return $self->configFacadeMock;
        });

        $this->logMonologServiceProvider = new LogMonologServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->logMonologServiceProvider->register($this->container);

        self::assertInstanceOf(
            LogFacadeInterface::class,
            $this->container->offsetGet(LogConstants::FACADE)
        );
    }
}
