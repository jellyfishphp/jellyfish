<?php

namespace Jellyfish\Application;

use Codeception\Test\Unit;
use Jellyfish\Kernel\KernelInterface;
use League\Route\Router;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

class HttpTest extends Unit
{
    /**
     * @var \Jellyfish\Application\Http
     */
    protected $http;

    /**
     * @var \Jellyfish\Kernel\KernelInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $kernelMock;

    /**
     * @var \Pimple\Container|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $containerMock;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $requestMock;

    /**
     * @var \Psr\Http\Message\ResponseInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $responseMock;

    /**
     * @var \League\Route\Router|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $routerMock;

    /**
     * @var \Zend\HttpHandlerRunner\Emitter\EmitterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $emitterMock;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->requestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->emitterMock = $this->getMockBuilder(EmitterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->kernelMock = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->http = new Http($this->kernelMock);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->kernelMock->expects($this->atLeastOnce())
            ->method('getContainer')
            ->willReturn($this->containerMock);

        $this->containerMock->expects($this->atLeastOnce())
            ->method('offsetGet')
            ->withConsecutive(['request'], ['router'], ['emitter'])
            ->willReturnOnConsecutiveCalls($this->requestMock, $this->routerMock, $this->emitterMock);

        $this->routerMock->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with($this->requestMock)
            ->willReturn($this->responseMock);

        $this->emitterMock->expects($this->atLeastOnce())
            ->method('emit')
            ->with($this->responseMock);


        $this->http->run();
    }
}
