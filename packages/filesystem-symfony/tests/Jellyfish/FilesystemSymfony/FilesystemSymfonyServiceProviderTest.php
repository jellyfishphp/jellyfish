<?php

declare(strict_types=1);

namespace Jellyfish\FilesystemSymfony;

use Codeception\Test\Unit;
use Jellyfish\Filesystem\FilesystemConstants;
use Jellyfish\Filesystem\FilesystemFacadeInterface;
use Pimple\Container;

class FilesystemSymfonyServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\FilesystemSymfony\FilesystemSymfonyServiceProvider
     */
    protected $filesystemSymfonyServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->filesystemSymfonyServiceProvider = new FilesystemSymfonyServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->filesystemSymfonyServiceProvider->register($this->container);

        static::assertInstanceOf(
            FilesystemFacadeInterface::class,
            $this->container->offsetGet(FilesystemConstants::FACADE)
        );
    }
}
