<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;

use function sprintf;

class EventQueueNameGeneratorTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventQueueNameGeneratorInterface
     */
    protected $eventQueueNameGenerator;

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
        $expectedEventQueueName = sprintf('%s_%s', $eventName, $eventListenerIdentifier);


        $this->assertEquals(
            $expectedEventQueueName,
            $this->eventQueueNameGenerator->generate($eventName, $eventListenerIdentifier)
        );
    }
}
