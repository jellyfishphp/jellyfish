<?php

namespace Jellyfish\SerializerSymfony;

use ArrayObject;
use Codeception\Test\Unit;
use stdClass;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerTest extends Unit
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $symfonySerializerMock;

    /**
     * @var \Jellyfish\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonySerializerMock = $this->getMockBuilder(SerializerInterface::class)
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

        $this->symfonySerializerMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with([$arrayObjectItem], 'json')
            ->willReturn($expectedJson);

        $this->assertEquals($expectedJson, $this->serializer->serialize($objectToSerialize, 'json'));
    }

    /**
     * @return void
     */
    public function testSerialize(): void
    {
        $expectedJson = '{}';
        $objectToSerialize = new stdClass();

        $this->symfonySerializerMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with($objectToSerialize, 'json')
            ->willReturn($expectedJson);

        $this->assertEquals($expectedJson, $this->serializer->serialize($objectToSerialize, 'json'));
    }

    /**
     * @return void
     */
    public function testDeserializeWithArray(): void
    {
        $deserializedArrayItem = new stdClass();
        $deserializedArray = [$deserializedArrayItem];
        $jsonToDeserialize = '[{}]';

        $this->symfonySerializerMock->expects($this->atLeastOnce())
            ->method('deserialize')
            ->with($jsonToDeserialize, 'stdClass[]', 'json')
            ->willReturn($deserializedArray);

        $this->assertEquals(
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

        $this->symfonySerializerMock->expects($this->atLeastOnce())
            ->method('deserialize')
            ->with($jsonToDeserialize, 'stdClass', 'json')
            ->willReturn($deserializedObject);

        $this->assertEquals(
            $deserializedObject,
            $this->serializer->deserialize($jsonToDeserialize, 'stdClass', 'json')
        );
    }
}
