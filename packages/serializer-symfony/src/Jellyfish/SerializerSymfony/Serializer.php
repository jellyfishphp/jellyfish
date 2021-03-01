<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

use ArrayObject;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

use function is_array;

class Serializer implements SerializerInterface
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    protected $symfonySerializer;

    /**
     * @param \Symfony\Component\Serializer\SerializerInterface $symfonySerializer
     */
    public function __construct(SymfonySerializerInterface $symfonySerializer)
    {
        $this->symfonySerializer = $symfonySerializer;
    }

    /**
     * @param object $data
     * @param string $format
     *
     * @return string
     */
    public function serialize(object $data, string $format): string
    {
        if (!($data instanceof ArrayObject)) {
            return $this->symfonySerializer->serialize($data, $format, [
                'skip_null_values' => true
            ]);
        }

        $dataAsArray = $data->getArrayCopy();

        return $this->symfonySerializer->serialize($dataAsArray, $format, [
            'skip_null_values' => true
        ]);
    }

    /**
     * @param string $data
     * @param string $type
     * @param string $format
     *
     * @return object
     */
    public function deserialize(string $data, string $type, string $format): object
    {
        $deserializedData = $this->symfonySerializer->deserialize($data, $type, $format);

        if (is_array($deserializedData)) {
            return new ArrayObject($deserializedData);
        }

        return $deserializedData;
    }
}
