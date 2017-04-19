<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.04.17
 */

namespace Dopamedia\Serializer\Model\Normalizer;

use Dopamedia\Serializer\Model\Mapper\MapperInterface;

abstract class AbstractNormalizer
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * AbstractNormalizer constructor.
     * @param MapperInterface $mapper
     */
    public function __construct(MapperInterface $mapper = null)
    {
        $this->mapper = $mapper;
    }
}