<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use PHPUnit\Framework\MockObject\MockObject;
use Codeception\Test\Unit;
use Jellyfish\Filesystem\FilesystemInterface;
use Jellyfish\Finder\FinderFactoryInterface;
use Jellyfish\Serializer\SerializerInterface;
use Jellyfish\Transfer\Command\TransferGenerateCommand;
use org\bovigo\vfs\vfsStream;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use stdClass;

class TransferServiceProviderTest extends Unit
{
    protected Container $container;

    protected TransferServiceProvider $transferServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $rootDir = vfsStream::setup('root', null, [
            'src' => [
                'Generated' => [
                    'Transfer' => [
                        'factory-registry.php' => file_get_contents(codecept_data_dir('factory-registry.php'))
                    ]
                ]
            ]
        ])->url();

        $rootDir = rtrim($rootDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet('root_dir', $rootDir);

        $this->container->offsetSet('commands', static fn(): array => []);

        $this->container->offsetSet('serializer', static fn(): MockObject => $self->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet('finder_factory', static fn(): MockObject => $self->getMockBuilder(FinderFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet('filesystem', static fn(): MockObject => $self->getMockBuilder(FilesystemInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->container->offsetSet('logger', static fn(): MockObject => $self->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

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

        $this->assertTrue($this->container->offsetExists('generated_transfer_test_factory'));

        $testFactory = $this->container->offsetGet('generated_transfer_test_factory');

        $this->assertInstanceOf(stdClass::class, $testFactory);
    }
}
