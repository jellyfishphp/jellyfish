<?php

namespace Jellyfish\SerializerSymfony;

use ArrayObject;
use Jellyfish\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

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
            return $this->symfonySerializer->serialize($data, $format);
        }

        $dataAsArray = $data->getArrayCopy();

        return $this->symfonySerializer->serialize($dataAsArray, $format);
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
        if (substr($type, -2) !== '[]') {
            return $this->symfonySerializer->deserialize($data, $type, $format);
        }

        return new ArrayObject($this->symfonySerializer->deserialize($data, $type, $format));
    }
}
