<?php

declare(strict_types=1);

namespace Jellyfish\HttpLeague;

use Codeception\Test\Unit;
use Jellyfish\Http\ControllerInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use League\Route\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class HttpLeagueFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\HttpLeague\HttpLeagueFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $httpLeagueFactoryMock;

    /**
     * @var \League\Route\Router|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $routerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ServerRequestInterface
     */
    protected $serverRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ServerRequestInterface
     */
    protected $emitterMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Message\ResponseInterface
     */
    protected $responseMock;

    /**
     * @var \Jellyfish\Http\ControllerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $controllerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Http\Server\MiddlewareInterface
     */
    protected $middlewareMock;

    /**
     * @var \Jellyfish\HttpLeague\HttpLeagueFacade
     */
    protected HttpLeagueFacade $httpLeagueFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->httpLeagueFactoryMock = $this->getMockBuilder(HttpLeagueFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serverRequestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->emitterMock = $this->getMockBuilder(EmitterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controllerMock = $this->getMockBuilder(ControllerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->middlewareMock = $this->getMockBuilder(MiddlewareInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpLeagueFacade = new HttpLeagueFacade($this->httpLeagueFactoryMock);
    }

    /**
     * @return void
     */
    public function testDispatch(): void
    {
        $this->httpLeagueFactoryMock->expects(static::atLeastOnce())
            ->method('getRouter')
            ->willReturn($this->routerMock);

        $this->routerMock->expects(static::atLeastOnce())
            ->method('dispatch')
            ->with($this->serverRequestMock)
            ->willReturn($this->responseMock);

        static::assertEquals(
            $this->responseMock,
            $this->httpLeagueFacade->dispatch($this->serverRequestMock)
        );
    }

    /**
     * @return void
     */
    public function testEmit(): void
    {
        $this->httpLeagueFactoryMock->expects(static::atLeastOnce())
            ->method('getEmitter')
            ->willReturn($this->emitterMock);

        $this->emitterMock->expects(static::atLeastOnce())
            ->method('emit')
            ->with($this->responseMock)
            ->willReturn(true);

        static::assertTrue($this->httpLeagueFacade->emit($this->responseMock));
    }

    /**
     * @return void
     */
    public function testMap(): void
    {
        $method = 'GET';
        $path = '/foo';

        $this->httpLeagueFactoryMock->expects(static::atLeastOnce())
            ->method('getRouter')
            ->willReturn($this->routerMock);

        $this->routerMock->expects(static::atLeastOnce())
            ->method('map')
            ->with($method, $path, $this->controllerMock);

        static::assertEquals(
            $this->httpLeagueFacade,
            $this->httpLeagueFacade->map($method, $path, $this->controllerMock)
        );
    }

    /**
     * @return void
     */
    public function testGetCurrentRequest(): void
    {
        $this->httpLeagueFactoryMock->expects(static::atLeastOnce())
            ->method('getRequest')
            ->willReturn($this->serverRequestMock);

        static::assertEquals(
            $this->serverRequestMock,
            $this->httpLeagueFacade->getCurrentRequest()
        );
    }

    /**
     * @return void
     */
    public function testAddMiddleware(): void
    {
        $this->httpLeagueFactoryMock->expects(static::atLeastOnce())
            ->method('getRouter')
            ->willReturn($this->routerMock);

        $this->routerMock->expects(static::atLeastOnce())
            ->method('middleware')
            ->with($this->middlewareMock);

        static::assertEquals(
            $this->httpLeagueFacade,
            $this->httpLeagueFacade->addMiddleware($this->middlewareMock)
        );
    }
}
