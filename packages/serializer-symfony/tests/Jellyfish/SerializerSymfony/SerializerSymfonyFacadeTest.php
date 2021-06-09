<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use Codeception\Test\Unit;
use Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface;
use Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProvider;
use stdClass;

class SerializerSymfonyFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\SerializerSymfony\SerializerSymfonyFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerSymfonyFactoryMock;

    /**
     * @var \Jellyfish\SerializerSymfony\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerMock;

    /**
     * @var \Jellyfish\SerializerSymfony\NameConverter\PropertyNameConverterStrategyProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $propertyNameConverterStrategyProviderMock;

    /**
     * @var \Jellyfish\Serializer\NameConverter\PropertyNameConverterStrategyInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $propertyNameConverterStrategyMock;

    /**
     * @var string
     */
    protected string $propertyNameConverterStrategyKey;

    /**
     * @var \Jellyfish\SerializerSymfony\SerializerSymfonyFacade
     */
    protected SerializerSymfonyFacade $serializerSymfonyFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->serializerSymfonyFactoryMock = $this->getMockBuilder(SerializerSymfonyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyNameConverterStrategyProviderMock = $this->getMockBuilder(PropertyNameConverterStrategyProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyNameConverterStrategyMock = $this->getMockBuilder(PropertyNameConverterStrategyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyNameConverterStrategyKey = 'foo';

        $this->serializerSymfonyFacade = new SerializerSymfonyFacade(
            $this->serializerSymfonyFactoryMock
        );
    }

    /**
     * @return void
     */
    public function testSerialize(): void
    {
        $expectedJson = '{}';
        $objectToSerialize = new stdClass();

        $this->serializerSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getSerializer')
            ->willReturn($this->serializerMock);

        $this->serializerMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->with($objectToSerialize, 'json')
            ->willReturn($expectedJson);

        static::assertEquals(
            $expectedJson,
            $this->serializerSymfonyFacade->serialize($objectToSerialize, 'json')
        );
    }

    /**
     * @return void
     */
    public function testDeserialize(): void
    {
        $deserializedObject = new stdClass();
        $jsonToDeserialize = '{}';

        $this->serializerSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getSerializer')
            ->willReturn($this->serializerMock);

        $this->serializerMock->expects(static::atLeastOnce())
            ->method('deserialize')
            ->with($jsonToDeserialize, 'stdClass', 'json')
            ->willReturn($deserializedObject);


        static::assertEquals(
            $deserializedObject,
            $this->serializerSymfonyFacade->deserialize($jsonToDeserialize, 'stdClass', 'json')
        );
    }

    /**
     * @return void
     */
    public function testAddPropertyNameConverterStrategy(): void
    {
        $this->serializerSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getPropertyNameConverterStrategyProvider')
            ->willReturn($this->propertyNameConverterStrategyProviderMock);

        $this->propertyNameConverterStrategyProviderMock->expects(static::atLeastOnce())
            ->method('add')
            ->with($this->propertyNameConverterStrategyKey, $this->propertyNameConverterStrategyMock)
            ->willReturn($this->propertyNameConverterStrategyProviderMock);

        static::assertEquals(
            $this->serializerSymfonyFacade,
            $this->serializerSymfonyFacade->addPropertyNameConverterStrategy(
                $this->propertyNameConverterStrategyKey,
                $this->propertyNameConverterStrategyMock
            )
        );
    }

    /**
     * @return void
     */
    public function testGetAllPropertyNameConverterStrategies(): void
    {
        $allPropertyNameConverterStrategies = [
            $this->propertyNameConverterStrategyProviderMock
        ];

        $this->serializerSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getPropertyNameConverterStrategyProvider')
            ->willReturn($this->propertyNameConverterStrategyProviderMock);

        $this->propertyNameConverterStrategyProviderMock->expects(static::atLeastOnce())
            ->method('getAll')
            ->willReturn($allPropertyNameConverterStrategies);

        static::assertEquals(
            $allPropertyNameConverterStrategies,
            $this->serializerSymfonyFacade->getAllPropertyNameConverterStrategies()
        );
    }

    /**
     * @return void
     */
    public function testGetPropertyNameConverterStrategy(): void
    {
        $this->serializerSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getPropertyNameConverterStrategyProvider')
            ->willReturn($this->propertyNameConverterStrategyProviderMock);

        $this->propertyNameConverterStrategyProviderMock->expects(static::atLeastOnce())
            ->method('get')
            ->with($this->propertyNameConverterStrategyKey)
            ->willReturn($this->propertyNameConverterStrategyMock);

        static::assertEquals(
            $this->propertyNameConverterStrategyMock,
            $this->serializerSymfonyFacade->getPropertyNameConverterStrategy($this->propertyNameConverterStrategyKey)
        );
    }

    /**
     * @return void
     */
    public function testHasPropertyNameConverterStrategy(): void
    {
        $this->serializerSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getPropertyNameConverterStrategyProvider')
            ->willReturn($this->propertyNameConverterStrategyProviderMock);

        $this->propertyNameConverterStrategyProviderMock->expects(static::atLeastOnce())
            ->method('has')
            ->with($this->propertyNameConverterStrategyKey)
            ->willReturn(true);

        static::assertTrue(
            $this->serializerSymfonyFacade->hasPropertyNameConverterStrategy($this->propertyNameConverterStrategyKey)
        );
    }

    /**
     * @return void
     */
    public function testRemovePropertyNameConverterStrategy(): void
    {
        $this->serializerSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('getPropertyNameConverterStrategyProvider')
            ->willReturn($this->propertyNameConverterStrategyProviderMock);

        $this->propertyNameConverterStrategyProviderMock->expects(static::atLeastOnce())
            ->method('remove')
            ->with($this->propertyNameConverterStrategyKey)
            ->willReturn($this->propertyNameConverterStrategyProviderMock);

        static::assertEquals(
            $this->serializerSymfonyFacade,
            $this->serializerSymfonyFacade->removePropertyNameConverterStrategy($this->propertyNameConverterStrategyKey)
        );
    }
}
