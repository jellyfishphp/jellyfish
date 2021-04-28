<?php

declare(strict_types=1);

namespace Jellyfish\UuidRamsey;

use Codeception\Test\Unit;

class UuidRamseyFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\UuidRamsey\UuidRamseyFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $uuidRamseyFactoryMock;

    /**
     * @var \Jellyfish\UuidRamsey\UuidGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $uuidGeneratorMock;

    /**
     * @var \Jellyfish\UuidRamsey\UuidRamseyFacade
     */
    protected UuidRamseyFacade $uuidRamseyFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->uuidRamseyFactoryMock = $this->getMockBuilder(UuidRamseyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uuidGeneratorMock = $this->getMockBuilder(UuidGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uuidRamseyFacade = new UuidRamseyFacade($this->uuidRamseyFactoryMock);
    }

    /**
     * @return void
     */
    public function testGenerateUuid(): void
    {
        $uuid = 'c470aa7e-0e14-4fa4-83bb-2e95c85fe1f7';

        $this->uuidRamseyFactoryMock->expects(static::atLeastOnce())
            ->method('createUuidGenerator')
            ->willReturn($this->uuidGeneratorMock);

        $this->uuidGeneratorMock->expects(static::atLeastOnce())
            ->method('generate')
            ->willReturn($uuid);

        static::assertEquals(
            $uuid,
            $this->uuidRamseyFacade->generateUuid()
        );
    }
}
