<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Codeception\Test\Unit;
use Jellyfish\Console\ConsoleConstants;
use Jellyfish\Console\ConsoleFacadeInterface;
use Jellyfish\Filesystem\FilesystemConstants;
use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Jellyfish\Finder\FinderConstants;
use Jellyfish\Finder\FinderFacadeInterface;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Jellyfish\Transfer\Command\TransferGenerateCommand;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;

class TransferServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Console\ConsoleFacadeInterface
     */
    protected $consoleFacadeMock;

    /**
     * @var \Pimple\Container
     */
    protected Container $container;

    /**
     * @var \Jellyfish\Transfer\TransferServiceProvider
     */
    protected TransferServiceProvider $transferServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->consoleFacadeMock = $this->getMockBuilder(ConsoleFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet('root_dir', DIRECTORY_SEPARATOR);

        $this->container->offsetSet(ConsoleConstants::FACADE, fn () => $self->consoleFacadeMock);

        $this->container->offsetSet(SerializerConstants::FACADE, fn () => $self->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet(FinderConstants::FACADE, fn () => $self->getMockBuilder(FinderFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet(FilesystemConstants::FACADE, fn () => $self->getMockBuilder(FilesystemFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->transferServiceProvider = new TransferServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->consoleFacadeMock->expects(static::atLeastOnce())
            ->method('addCommand')
            ->with(
                static::callback(static fn (Command $command) => $command instanceof TransferGenerateCommand)
            )->willReturn($this->consoleFacadeMock);

        $this->transferServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(ConsoleConstants::FACADE));
        static::assertInstanceOf(
            ConsoleFacadeInterface::class,
            $this->container->offsetGet(ConsoleConstants::FACADE)
        );

        static::assertTrue($this->container->offsetExists(TransferConstants::FACADE));
        static::assertInstanceOf(
            TransferFacadeInterface::class,
            $this->container->offsetGet(TransferConstants::FACADE)
        );
    }
}
