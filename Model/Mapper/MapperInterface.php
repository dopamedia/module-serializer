<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.04.17
 */

namespace Dopamedia\Serializer\Model\Mapper;

interface MapperInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function map(array $data): array;
}