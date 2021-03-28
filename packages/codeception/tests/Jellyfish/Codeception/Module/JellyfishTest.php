<?php

declare(strict_types=1);

namespace Jellyfish\Codeception\Module;

use Codeception\Lib\ModuleContainer;
use Codeception\Test\Unit;
use Jellyfish\Transfer\TransferFacadeInterface;

class JellyfishTest extends Unit
{
    /**
     * @var \Codeception\Lib\ModuleContainer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $moduleContainerMock;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Jellyfish\Transfer\TransferFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transferFacadeMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->moduleContainerMock = $this->getMockBuilder(ModuleContainer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = [
            JellyfishConstants::CONFIG_GENERATE_TRANSFER_CLASSES => false
        ];

        $this->transferFacadeMock = $this->getMockBuilder(TransferFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \Jellyfish\Codeception\Module\Jellyfish
     */
    protected function getJellyfish(): Jellyfish
    {
        return new class($this->transferFacadeMock, $this->moduleContainerMock, $this->config) extends Jellyfish {
            /**
             * @param \Jellyfish\Transfer\TransferFacadeInterface $transferFacade
             * @param \Codeception\Lib\ModuleContainer $moduleContainer
             * @param array|null $config
             */
            public function __construct(
                TransferFacadeInterface $transferFacade,
                ModuleContainer $moduleContainer,
                $config = null
            ) {
                parent::__construct($moduleContainer, $config);

                $this->transferFacade = $transferFacade;
            }
        };
    }

    /**
     * @return void
     */
    public function testInitializeWithDefaultConfig(): void
    {
        $this->transferFacadeMock->expects(static::never())
            ->method('clean');

        $this->transferFacadeMock->expects(static::never())
            ->method('generate');

        $this->getJellyfish()->_initialize();
    }

    /**
     * @return void
     */
    public function testInitialize(): void
    {
        $this->transferFacadeMock->expects(static::atLeastOnce())
            ->method('clean');

        $this->transferFacadeMock->expects(static::atLeastOnce())
            ->method('generate');

        $this->config[JellyfishConstants::CONFIG_GENERATE_TRANSFER_CLASSES] = true;

        $this->getJellyfish()->_initialize();
    }
}
