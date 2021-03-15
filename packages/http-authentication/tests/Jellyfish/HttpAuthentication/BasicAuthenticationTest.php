<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Codeception\Test\Unit;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

use function password_hash;

class BasicAuthenticationTest extends Unit
{
    /**
     * @var \Jellyfish\HttpAuthentication\UserReaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $userReaderMock;

    /**
     * @var \Jellyfish\HttpAuthentication\UserInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $userMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ServerRequestInterface
     */
    protected $serverRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\UriInterface
     */
    protected $uriMock;

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

        $this->userReaderMock = $this->getMockBuilder(UserReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userMock = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uriMock = $this->getMockBuilder(UriInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->basicAuthentication = new BasicAuthentication($this->userReaderMock);
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

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getUri')
            ->willReturn($this->uriMock);

        $this->uriMock->expects(static::atLeastOnce())
            ->method('getPath')
            ->willReturn('/');

        $this->userReaderMock->expects(static::atLeastOnce())
            ->method('getByIdentifier')
            ->with('foo')
            ->willReturn($this->userMock);

        $this->userMock->expects(static::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('foo');

        $this->userMock->expects(static::atLeastOnce())
            ->method('getPassword')
            ->willReturn(password_hash('bar', PASSWORD_BCRYPT));

        $this->userMock->expects(static::atLeastOnce())
            ->method('getPathRegEx')
            ->willReturn('/\/.*/');

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

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getUri')
            ->willReturn($this->uriMock);

        $this->uriMock->expects(static::atLeastOnce())
            ->method('getPath')
            ->willReturn('/');

        $this->userReaderMock->expects(static::never())
            ->method('getByIdentifier');

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
            ->willReturn(['Basic Zm9vOmJhcg==']);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getUri')
            ->willReturn($this->uriMock);

        $this->uriMock->expects(static::atLeastOnce())
            ->method('getPath')
            ->willReturn('/');

        $this->userReaderMock->expects(static::atLeastOnce())
            ->method('getByIdentifier')
            ->with('foo')
            ->willReturn($this->userMock);

        $this->userMock->expects(static::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('foo');

        $this->userMock->expects(static::atLeastOnce())
            ->method('getPassword')
            ->willReturn('baa');

        static::assertFalse($this->basicAuthentication->authenticate($this->serverRequestMock));
    }

    /**
     * @return void
     */
    public function testAuthenticateWithNonExistingUser(): void
    {
        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('hasHeader')
            ->with('Authorization')
            ->willReturn(true);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn(['Basic Zm9vOmJhcg==']);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getUri')
            ->willReturn($this->uriMock);

        $this->uriMock->expects(static::atLeastOnce())
            ->method('getPath')
            ->willReturn('/');

        $this->userReaderMock->expects(static::atLeastOnce())
            ->method('getByIdentifier')
            ->with('foo')
            ->willReturn(null);

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

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getUri')
            ->willReturn($this->uriMock);

        $this->uriMock->expects(static::atLeastOnce())
            ->method('getPath')
            ->willReturn('/');

        $this->userReaderMock->expects(static::never())
            ->method('getByIdentifier');

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
            ->willReturn(['Basic Zm9v']);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getUri')
            ->willReturn($this->uriMock);

        $this->uriMock->expects(static::atLeastOnce())
            ->method('getPath')
            ->willReturn('/');

        $this->userReaderMock->expects(static::never())
            ->method('getByIdentifier');

        static::assertFalse($this->basicAuthentication->authenticate($this->serverRequestMock));
    }

    /**
     * @return void
     */
    public function testAuthenticateWithInvalidAuthorizationHeaderType(): void
    {
        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('hasHeader')
            ->with('Authorization')
            ->willReturn(true);

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getHeader')
            ->with('Authorization')
            ->willReturn('Basic Zm9v');

        $this->serverRequestMock->expects(static::atLeastOnce())
            ->method('getUri')
            ->willReturn($this->uriMock);

        $this->uriMock->expects(static::atLeastOnce())
            ->method('getPath')
            ->willReturn('/');

        $this->userReaderMock->expects(static::never())
            ->method('getByIdentifier');

        static::assertFalse($this->basicAuthentication->authenticate($this->serverRequestMock));
    }
}
