<?php

namespace Jellyfish\UuidRamsey;

use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

class UuidGeneratorTest extends Unit
{
    protected MockObject&UuidFactoryInterface $uuidFactoryMock;

    protected MockObject&UuidInterface $uuidMock;

    protected UuidGenerator $uuidGenerator;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->uuidFactoryMock = $this->getMockBuilder(UuidFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uuidMock = $this->getMockBuilder(UuidInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uuidGenerator = new UuidGenerator($this->uuidFactoryMock);
    }

    /**
     * @return void
     */
    public function testGenerate(): void
    {
        $uuid4 = 'c470aa7e-0e14-4fa4-83bb-2e95c85fe1f7';

        $this->uuidFactoryMock->expects($this->atLeastOnce())
            ->method('uuid4')
            ->willReturn($this->uuidMock);

        $this->uuidMock->expects($this->atLeastOnce())
            ->method('toString')
            ->willReturn($uuid4);

        self::assertEquals($uuid4, $this->uuidGenerator->generate());
    }
}
