<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Codeception\Test\Unit;
use Psr\Http\Message\ServerRequestInterface;

class BasicAuthenticationTest extends Unit
{
    /**
     * @var \Jellyfish\HttpAuthentication\UserInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $userMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ServerRequestInterface
     */
    protected $serverRequestMock;

    /**
     * @var \Jellyfish\HttpAuthentication\BasicAuthentication
     */
    protected $basicAuthentication;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->userMock = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->basicAuthentication = new BasicAuthentication($this->userMock);
    }

    /**
     * @return void
     */
    public function testAuthenticate(): void
    {
        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('hasHeader')
            ->with('Authorization')
            ->willReturn(true);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn(['Basic Zm9vOmJhcg==']);

        $this->userMock->expects(static::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('foo');

        $this->userMock->expects(static::atLeastOnce())
            ->method('getPassword')
            ->willReturn('bar');

        static::assertTrue($this->basicAuthentication->authenticate($this->serverRequestMock));
    }

    /**
     * @return void
     */
    public function testAuthenticateWithInvalidType(): void
    {
        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('hasHeader')
            ->with('Authorization')
            ->willReturn(true);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn(['Bearer Zm9vOmJhcg==']);

        $this->userMock->expects(static::never())
            ->method('getIdentifier');

        $this->userMock->expects(static::never())
            ->method('getPassword');

        static::assertFalse($this->basicAuthentication->authenticate($this->serverRequestMock));
    }

    /**
     * @return void
     */
    public function testAuthenticateWithInvalidCredentials(): void
    {
        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('hasHeader')
            ->with('Authorization')
            ->willReturn(true);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn(['Basic Zi9vOmJhcg==']);

        $this->userMock->expects(static::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('foo');

        $this->userMock->expects(static::never())
            ->method('getPassword');

        static::assertFalse($this->basicAuthentication->authenticate($this->serverRequestMock));
    }

    /**
     * @return void
     */
    public function testAuthenticateWithoutAuthorizationHeader(): void
    {
        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('hasHeader')
            ->with('Authorization')
            ->willReturn(false);

        $this->serverRequestMock->expects(static::never())
            ->method('getHeader')
            ->with('Authorization');

        $this->userMock->expects(static::never())
            ->method('getIdentifier');

        $this->userMock->expects(static::never())
            ->method('getPassword');

        static::assertFalse($this->basicAuthentication->authenticate($this->serverRequestMock));
    }

    /**
     * @return void
     */
    public function testAuthenticateWithInvalidAuthorizationHeader(): void
    {
        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('hasHeader')
            ->with('Authorization')
            ->willReturn(true);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn('foo');

        $this->userMock->expects(static::never())
            ->method('getIdentifier');

        $this->userMock->expects(static::never())
            ->method('getPassword');

        static::assertFalse($this->basicAuthentication->authenticate($this->serverRequestMock));
    }
}
