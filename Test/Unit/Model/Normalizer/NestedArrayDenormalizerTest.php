<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.04.17
 */

namespace Dopamedia\Serializer\Test\Unit\Model\Normalizer;

use Dopamedia\Serializer\Model\Normalizer\NestedArrayDenormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class NestedArrayDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    /**
     * @var NestedArrayDenormalizer
     */
    private $denormalizer;

    protected function setUp()
    {
        $this->serializer = $this->getMockBuilder('Symfony\Component\Serializer\Serializer')->getMock();
        $this->denormalizer = new NestedArrayDenormalizer();
        $this->denormalizer->setSerializer($this->serializer);
    }

    public function testDenormalize()
    {
        $this->serializer->expects($this->at(0))
            ->method('denormalize')
            ->will($this->returnValue(['foo' => 'bar']));

        $this->serializer->expects($this->at(1))
            ->method('denormalize')
            ->will($this->returnValue(['bar' => 'baz']));

        $result = $this->denormalizer->denormalize(
            [[], []],
            'array[]'
        );

        $this->assertEquals([['foo' => 'bar'], ['bar' => 'baz']], $result);
    }

    public function testDenormalizeMultidimensional()
    {
        $this->serializer->expects($this->at(0))
            ->method('denormalize')
            ->will($this->returnValue([['foo' => 'bar'], ['bar' => 'baz']]));

        $this->serializer->expects($this->at(1))
            ->method('denormalize')
            ->will($this->returnValue(['lorem' => 'ipsum']));

        $result = $this->denormalizer->denormalize(
            [[], []],
            'array[]'
        );

        $this->assertEquals([['foo' => 'bar'], ['bar' => 'baz'], ['lorem' => 'ipsum']], $result);
    }
}
