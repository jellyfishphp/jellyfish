<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigFacadeInterface;

class HttpAuthenticationFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @var \Jellyfish\HttpAuthentication\HttpAuthenticationFactory
     */
    protected $httpAuthenticationFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpAuthenticationFactory = new HttpAuthenticationFactory($this->configFacadeMock);
    }

    /**
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     *
     * @return void
     */
    public function testGetAuthentication(): void
    {
        $this->configFacadeMock->expects(static::atLeastOnce())
            ->method('get')
            ->withConsecutive(
                [HttpAuthenticationConstants::USER_IDENTIFIER, HttpAuthenticationConstants::DEFAULT_USER_IDENTIFIER],
                [HttpAuthenticationConstants::USER_PASSWORD, HttpAuthenticationConstants::DEFAULT_USER_PASSWORD],
            )->willReturnOnConsecutiveCalls(
                HttpAuthenticationConstants::DEFAULT_USER_IDENTIFIER,
                HttpAuthenticationConstants::DEFAULT_USER_PASSWORD
            );

        static::assertInstanceOf(
            BasicAuthentication::class,
            $this->httpAuthenticationFactory->getAuthentication()
        );
    }
}
