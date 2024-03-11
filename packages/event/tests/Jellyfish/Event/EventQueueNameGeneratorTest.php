<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;

class EventQueueNameGeneratorTest extends Unit
{
    protected EventQueueNameGenerator $eventQueueNameGenerator;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventQueueNameGenerator = new EventQueueNameGenerator();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $eventName = 'test';
        $eventListenerIdentifier = 'testListener';
        $expectedEventQueueName = \sprintf('%s_%s', $eventName, $eventListenerIdentifier);


        $this->assertSame(
            $expectedEventQueueName,
            $this->eventQueueNameGenerator->generate($eventName, $eventListenerIdentifier),
        );
    }
}
