<?php

declare(strict_types = 1);

namespace Jellyfish\SerializerSymfony;

use ArrayObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerTest extends Unit
{
    protected SerializerInterface&MockObject $symfonySerializerMock;

    protected Serializer $serializer;

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
            ->with([$arrayObjectItem], 'json', ['skip_null_values' => true])
            ->willReturn($expectedJson);

        $this->assertSame($expectedJson, $this->serializer->serialize($objectToSerialize, 'json'));
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
            ->with($objectToSerialize, 'json', ['skip_null_values' => true])
            ->willReturn($expectedJson);

        $this->assertSame($expectedJson, $this->serializer->serialize($objectToSerialize, 'json'));
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
            $this->serializer->deserialize($jsonToDeserialize, 'stdClass[]', 'json'),
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
            $this->serializer->deserialize($jsonToDeserialize, 'stdClass', 'json'),
        );
    }
}
