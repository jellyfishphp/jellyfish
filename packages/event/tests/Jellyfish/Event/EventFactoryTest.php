<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Uuid\UuidGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;

class EventFactoryTest extends Unit
{
    protected MockObject&UuidGeneratorInterface $uuidGeneratorMock;

    protected EventFactory $eventFactory;

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

        $this->assertInstanceOf(Event::class, $this->eventFactory->create());
    }
}
