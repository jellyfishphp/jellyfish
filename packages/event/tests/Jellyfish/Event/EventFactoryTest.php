<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Uuid\UuidFacadeInterface;

class EventFactoryTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Uuid\UuidFacadeInterface
     */
    protected $uuidFacadeMock;

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

        $this->uuidFacadeMock = $this->getMockBuilder(UuidFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventFactory = new EventFactory($this->uuidFacadeMock);
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $this->uuidFacadeMock->expects(static::atLeastOnce())
            ->method('generateUuid')
            ->willReturn('294452fd-0ba8-481c-8cfd-832a68c2edc3');

        static::assertInstanceOf(Event::class, $this->eventFactory->create());
    }
}
