<?php

declare(strict_types=1);

namespace Jellyfish\UuidRamsey;

use Codeception\Test\Unit;

class UuidRamseyFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\UuidRamsey\UuidRamseyFactory|null
     */
    protected ?UuidRamseyFactory $uuidRamseyFactory = null;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->uuidRamseyFactory = new UuidRamseyFactory();
    }

    /**
     * @return void
     */
    public function testCreateUuidGenerator(): void
    {
        static::assertInstanceOf(
            UuidGenerator::class,
            $this->uuidRamseyFactory->createUuidGenerator()
        );
    }
}
