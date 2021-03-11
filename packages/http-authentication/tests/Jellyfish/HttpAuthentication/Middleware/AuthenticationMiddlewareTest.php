<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication\Middleware;

use Codeception\Test\Unit;
use Jellyfish\HttpAuthentication\HttpAuthenticationFacadeInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddlewareTest extends Unit
{
    /**
     * @var \Jellyfish\HttpAuthentication\HttpAuthenticationFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $httpAuthenticationFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ServerRequestInterface
     */
    protected $serverRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ResponseInterface
     */
    protected $responseMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Server\RequestHandlerInterface
     */
    protected $requestHandlerMock;

    /**
     * @var \Jellyfish\HttpAuthentication\Middleware\AuthenticationMiddleware
     */
    protected $authenticationMiddleware;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->httpAuthenticationFacadeMock = $this->getMockBuilder(HttpAuthenticationFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestHandlerMock = $this->getMockBuilder(RequestHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->authenticationMiddleware = new AuthenticationMiddleware($this->httpAuthenticationFacadeMock);
    }

    /**
     * @return void
     */
    public function testProcess(): void
    {
        $this->httpAuthenticationFacadeMock->expects(static::atLeastOnce())
            ->method('authenticate')
            ->with($this->serverRequestMock)
            ->willReturn(true);

        $this->requestHandlerMock->expects(static::atLeastOnce())
            ->method('handle')
            ->with($this->serverRequestMock)
            ->willReturn($this->responseMock);

        static::assertEquals(
            $this->responseMock,
            $this->authenticationMiddleware->process($this->serverRequestMock, $this->requestHandlerMock)
        );
    }

    /**
     * @return void
     */
    public function testProcessWithInvalidServerRequest(): void
    {
        $this->httpAuthenticationFacadeMock->expects(static::atLeastOnce())
            ->method('authenticate')
            ->with($this->serverRequestMock)
            ->willReturn(false);

        $this->requestHandlerMock->expects(static::never())
            ->method('handle')
            ->with($this->serverRequestMock);

        $response = $this->authenticationMiddleware->process($this->serverRequestMock, $this->requestHandlerMock);

        static::assertEquals(
            401,
            $response->getStatusCode()
        );

        static::assertInstanceOf(
            EmptyResponse::class,
            $response
        );
    }
}
