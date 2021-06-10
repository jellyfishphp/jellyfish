<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;

class EventErrorHandlerProviderTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventErrorHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventErrorHandlerMock;

    /**
     * @var \Jellyfish\Event\EventErrorHandlerProvider
     */
    protected EventErrorHandlerProvider $eventErrorHandlerProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventErrorHandlerMock = $this->getMockBuilder(EventErrorHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventErrorHandlerProvider = new EventErrorHandlerProvider();
    }

    /**
     * @return void
     */
    public function testAddAndGetAll(): void
    {
        static::assertEquals(
            $this->eventErrorHandlerProvider,
            $this->eventErrorHandlerProvider->add($this->eventErrorHandlerMock)
        );

        static::assertCount(1, $this->eventErrorHandlerProvider->getAll());
    }

    /**
     * @return void
     */
    public function testGetAll(): void
    {
        static::assertCount(0, $this->eventErrorHandlerProvider->getAll());
    }
}
