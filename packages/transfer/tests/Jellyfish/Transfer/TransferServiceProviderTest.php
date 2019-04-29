<?php

namespace Jellyfish\Transfer;

use Codeception\Test\Unit;
use Jellyfish\Filesystem\FilesystemInterface;
use Jellyfish\Finder\FinderFactoryInterface;
use Jellyfish\Serializer\SerializerInterface;
use Jellyfish\Transfer\Command\TransferGenerateCommand;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class TransferServiceProviderTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\TransferServiceProvider
     */
    protected $transferServiceProvider;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet('root_dir', function () {
            return DIRECTORY_SEPARATOR;
        });

        $this->container->offsetSet('commands', function () {
            return [];
        });

        $this->container->offsetSet('serializer', function () use ($self) {
            return $self->getMockBuilder(SerializerInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('finder_factory', function () use ($self) {
            return $self->getMockBuilder(FinderFactoryInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('filesystem', function () use ($self) {
            return $self->getMockBuilder(FilesystemInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet('logger', function () use ($self) {
            return $self->getMockBuilder(LoggerInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->transferServiceProvider = new TransferServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->transferServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists('commands'));

        $commands = $this->container->offsetGet('commands');

        $this->assertCount(1, $commands);
        $this->assertInstanceOf(TransferGenerateCommand::class, $commands[0]);
    }
}
