<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.04.17
 */

namespace Dopamedia\Serializer\Model\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class ArrayDenormalizer extends AbstractNormalizer implements DenormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if ($this->mapper !== null) {
            return $this->mapper->map($data);
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'array';
    }
}