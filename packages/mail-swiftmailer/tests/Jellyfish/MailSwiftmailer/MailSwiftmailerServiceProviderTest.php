<?php

namespace Jellyfish\MailSwiftmailer;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Mail\MailConstants;
use Jellyfish\Mail\MailFacadeInterface;
use Pimple\Container;

class MailSwiftmailerServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected Container $container;

    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @var \Jellyfish\MailSwiftmailer\MailSwiftmailerServiceProvider
     */
    protected MailSwiftmailerServiceProvider $mailSwiftmailerServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container->offsetSet(ConfigConstants::FACADE, static fn () => $self->configFacadeMock);

        $this->mailSwiftmailerServiceProvider = new MailSwiftmailerServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->mailSwiftmailerServiceProvider->register($this->container);

        self::assertInstanceOf(
            MailFacadeInterface::class,
            $this->container->offsetGet(MailConstants::FACADE)
        );
    }
}
