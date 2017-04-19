<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.04.17
 */

namespace Dopamedia\Serializer\Model\Normalizer;

use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class NestedArrayDenormalizer implements DenormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if ($this->serializer === null) {
            throw new BadMethodCallException('Please set a serializer before calling denormalize()!');
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException('Data expected to be an array, '.gettype($data).' given.');
        }

        if (substr($class, -2) !== '[]') {
            throw new InvalidArgumentException('Unsupported class: '.$class);
        }

        $class = substr($class, 0, -2);

        $result = [];
        foreach ($data as $value) {
            $denormalizedData = $this->serializer->denormalize($value, $class, $format, $context);

            // if denormalizedData is multidimensional add all elements separate
            if (is_array(current($denormalizedData))) {
                foreach ($denormalizedData as $denormalizedDataItem) {
                    $result[] = $denormalizedDataItem;
                }
            } else {
                $result[] = $denormalizedData;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return substr($type, -2) === '[]'
            && $this->serializer->supportsDenormalization($data, substr($type, 0, -2), $format);

    }
}