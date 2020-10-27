<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Uuid\UuidGeneratorInterface;

class EventFactoryTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Uuid\UuidGeneratorInterface
     */
    protected $uuidGeneratorMock;

    /**
     * @var \Jellyfish\Event\EventFactoryInterface
     */
    protected $eventFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->uuidGeneratorMock = $this->getMockBuilder(UuidGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventFactory = new EventFactory($this->uuidGeneratorMock);
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $this->uuidGeneratorMock->expects(self::atLeastOnce())
            ->method('generate')
            ->willReturn('294452fd-0ba8-481c-8cfd-832a68c2edc3');

        self::assertInstanceOf(Event::class, $this->eventFactory->create());
    }
}
