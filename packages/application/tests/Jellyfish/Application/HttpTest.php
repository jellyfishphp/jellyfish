<?php

declare(strict_types=1);

namespace Jellyfish\Application;

use Codeception\Test\Unit;
use Jellyfish\Http\HttpConstants;
use Jellyfish\Http\HttpFacadeInterface;
use Jellyfish\Kernel\KernelInterface;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpTest extends Unit
{
    /**
     * @var \Jellyfish\Kernel\KernelInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $kernelMock;

    /**
     * @var \Pimple\Container|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $containerMock;

    /**
     * @var \Jellyfish\Http\HttpFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $httpFacadeMock;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $requestMock;

    /**
     * @var \Psr\Http\Message\ResponseInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $responseMock;

    /**
     * @var \Jellyfish\Application\Http
     */
    protected Http $http;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->kernelMock = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpFacadeMock = $this->getMockBuilder(HttpFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
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
        $this->kernelMock->expects(static::atLeastOnce())
            ->method('getContainer')
            ->willReturn($this->containerMock);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('offsetGet')
            ->with(HttpConstants::FACADE)
            ->willReturn($this->httpFacadeMock);

        $this->httpFacadeMock->expects(static::atLeastOnce())
            ->method('getCurrentRequest')
            ->willReturn($this->requestMock);

        $this->httpFacadeMock->expects(static::atLeastOnce())
            ->method('dispatch')
            ->with($this->requestMock)
            ->willReturn($this->responseMock);

        $this->httpFacadeMock->expects(static::atLeastOnce())
            ->method('emit')
            ->with($this->responseMock)
            ->willReturn(true);

        $this->http->run();
    }
}
