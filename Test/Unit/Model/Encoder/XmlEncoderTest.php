<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.04.17
 */

namespace Dopamedia\Serializer\Test\Unit\Model\Encoder;

use Dopamedia\Serializer\Model\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class XmlEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XmlEncoder
     */
    private $encoder;

    protected function setUp()
    {
        $this->encoder = new XmlEncoder();
    }

    public function testDecodeWithEmptySource()
    {
        $this->setExpectedException(UnexpectedValueException::class);
        $this->encoder->decode('', 'xml');
    }

    public function testDecode()
    {
        $source = <<<XML
<?xml version="1.0"?>
<foo>bar</foo>
XML;
        $this->assertEquals(['foo' => 'bar'], $this->encoder->decode($source, 'xml'));
    }

    public function testDecodeWithCustomRootNode()
    {
        $source = <<<XML
<?xml version="1.0"?>
<catalog>
    <product>
        <name>foo</name>
    </product>
    <product>
        <name>bar</name>
    </product>
</catalog>
XML;

        $expected = [
            'product' =>
                [
                    [
                        'name' => 'foo'
                    ],
                    [
                        'name' => 'bar'
                    ]
                ]
        ];

        $encoder = new XmlEncoder('catalog');
        $this->assertEquals($expected, $encoder->decode($source, 'xml'));
    }

    public function testDecodeWithCustomChildNode()
    {
        $source = <<<XML
<?xml version="1.0"?>
<catalog>
    <product>
        <name>foo</name>
    </product>
    <product>
        <name>bar</name>
    </product>
</catalog>
XML;

        $expected = [
            [
                'name' => 'foo'
            ],
            [
                'name' => 'bar'
            ]
        ];

        $encoder = new XmlEncoder('catalog', 'product');
        $this->assertEquals($expected, $encoder->decode($source, 'xml'));
    }

}
