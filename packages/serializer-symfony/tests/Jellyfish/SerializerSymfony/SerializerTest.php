<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use ArrayObject;
use Codeception\Test\Unit;
use stdClass;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class SerializerTest extends Unit
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $symfonySerializerMock;

    /**
     * @var \Jellyfish\SerializerSymfony\SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonySerializerMock = $this->getMockBuilder(SymfonySerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializer = new Serializer($this->symfonySerializerMock);
    }

    /**
     * @return void
     */
    public function testSerializeWithArrayObject(): void
    {
        $expectedJson = '[{}]';
        $arrayObjectItem = new stdClass();
        $objectToSerialize = new ArrayObject([$arrayObjectItem]);

        $this->symfonySerializerMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->with([$arrayObjectItem], 'json', ['skip_null_values' => true])
            ->willReturn($expectedJson);

        static::assertEquals($expectedJson, $this->serializer->serialize($objectToSerialize, 'json'));
    }

    /**
     * @return void
     */
    public function testSerialize(): void
    {
        $expectedJson = '{}';
        $objectToSerialize = new stdClass();

        $this->symfonySerializerMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->with($objectToSerialize, 'json', ['skip_null_values' => true])
            ->willReturn($expectedJson);

        static::assertEquals($expectedJson, $this->serializer->serialize($objectToSerialize, 'json'));
    }

    /**
     * @return void
     */
    public function testDeserializeWithArray(): void
    {
        $deserializedArrayItem = new stdClass();
        $deserializedArray = [$deserializedArrayItem];
        $jsonToDeserialize = '[{}]';

        $this->symfonySerializerMock->expects(static::atLeastOnce())
            ->method('deserialize')
            ->with($jsonToDeserialize, 'stdClass[]', 'json')
            ->willReturn($deserializedArray);

        static::assertEquals(
            new ArrayObject($deserializedArray),
            $this->serializer->deserialize($jsonToDeserialize, 'stdClass[]', 'json')
        );
    }

    /**
     * @return void
     */
    public function testDeserialize(): void
    {
        $deserializedObject = new stdClass();
        $jsonToDeserialize = '{}';

        $this->symfonySerializerMock->expects(static::atLeastOnce())
            ->method('deserialize')
            ->with($jsonToDeserialize, 'stdClass', 'json')
            ->willReturn($deserializedObject);

        static::assertEquals(
            $deserializedObject,
            $this->serializer->deserialize($jsonToDeserialize, 'stdClass', 'json')
        );
    }
}
