<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Codeception\Test\Unit;
use Psr\Http\Message\ServerRequestInterface;

class HttpAuthenticationFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\HttpAuthentication\HttpAuthenticationFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $httpAuthenticationFactoryMock;

    /**
     * @var \Jellyfish\HttpAuthentication\AuthenticationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $authenticationMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ServerRequestInterface
     */
    protected $serverRequestMock;

    /**
     * @var \Jellyfish\HttpAuthentication\HttpAuthenticationFacade
     */
    protected HttpAuthenticationFacade $httpAuthenticationFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->httpAuthenticationFactoryMock = $this->getMockBuilder(HttpAuthenticationFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->authenticationMock = $this->getMockBuilder(AuthenticationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpAuthenticationFacade = new HttpAuthenticationFacade($this->httpAuthenticationFactoryMock);
    }

    /**
     * @return void
     */
    public function testAuthenticate(): void
    {
        $this->httpAuthenticationFactoryMock->expects(static::atLeastOnce())
            ->method('getAuthentication')
            ->willReturn($this->authenticationMock);

        $this->authenticationMock->expects(static::atLeastOnce())
            ->method('authenticate')
            ->with($this->serverRequestMock)
            ->willReturn(true);

        static::assertTrue($this->httpAuthenticationFacade->authenticate($this->serverRequestMock));
    }
}
