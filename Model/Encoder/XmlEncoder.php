<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.04.17
 */

namespace Dopamedia\Serializer\Model\Encoder;

use Magento\Framework\Xml\Parser;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerAwareTrait;

class XmlEncoder extends Parser implements EncoderInterface, DecoderInterface, NormalizationAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var null|string
     */
    private $rootNodeName;

    /**
     * @var null|string
     */
    private $childNodeName;

    /**
     * @inheritDoc
     */
    public function __construct(string $rootNodeName = null, string $childNodeName = null)
    {
        parent::__construct();
        $this->rootNodeName = $rootNodeName;
        $this->childNodeName = $childNodeName;
    }

    /**
     * @inheritDoc
     */
    public function decode($data, $format, array $context = array())
    {
        if (trim($data) === '') {
            throw new UnexpectedValueException('Invalid XML data, it can not be empty.');
        }

        return $this->loadXML($data)->xmlToArray();
    }

    /**
     * @inheritDoc
     */
    public function supportsDecoding($format)
    {
        return $format === 'xml';
    }

    /**
     * @inheritDoc
     */
    public function encode($data, $format, array $context = array())
    {
        // TODO: Implement encode() method.
    }

    /**
     * @inheritDoc
     */
    public function supportsEncoding($format)
    {
        // TODO: Implement supportsEncoding() method.
    }

    /**
     * @inheritDoc
     */
    public function xmlToArray()
    {
        $rootNode = false;
        if ($this->rootNodeName !== null) {
            $rootNode = $this->findRootNode($this->getDom());
        }

        $this->_content = $this->_xmlToArray($rootNode);
        return $this->_content;
    }

    /**
     * @param \DOMNode $currentNode
     * @return \DOMNode
     */
    protected function findRootNode(\DOMNode $currentNode): \DOMNode
    {
        if ($currentNode->nodeName === $this->rootNodeName) {
            return $currentNode;
        }

        if ($currentNode->hasChildNodes()) {
            foreach ($currentNode->childNodes as $childNode) {
                return $this->findRootNode($childNode);
            }
        }

        throw new BadMethodCallException(sprintf('unable to find rootNode with name "%s"', $this->rootNodeName));
    }

    /**
     * @param \DOMNode $currentNode
     * @return array
     */
    protected function _xmlToArray($currentNode = false)
    {
        if (!$currentNode) {
            $currentNode = $this->getDom();
        }

        $content = '';

        /** @var \DOMNode $node */
        foreach ($currentNode->childNodes as $node) {
            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:
                    $content = $content ?: [];
                    $value = null;
                    if ($node->hasChildNodes()) {
                        $value = $this->_xmlToArray($node);
                    }
                    $attributes = [];
                    if ($node->hasAttributes()) {
                        foreach ($node->attributes as $attribute) {
                            $attributes += [$attribute->name => $attribute->value];
                        }
                        $value = ['_value' => $value, '_attribute' => $attributes];
                    }

                    if ($this->childNodeName !== null && $node->nodeName === $this->childNodeName) {
                        $content[] = $value;
                    } elseif (isset($content[$node->nodeName])) {
                        if (!isset($content[$node->nodeName][0]) || !is_array($content[$node->nodeName][0])) {
                            $oldValue = $content[$node->nodeName];
                            $content[$node->nodeName] = [];
                            $content[$node->nodeName][] = $oldValue;
                        }
                        $content[$node->nodeName][] = $value;
                    } else {
                        $content[$node->nodeName] = $value;
                    }
                    break;
                case XML_CDATA_SECTION_NODE:
                    $content = $node->nodeValue;
                    break;
                case XML_TEXT_NODE:
                    if (trim($node->nodeValue) !== '') {
                        $content = $node->nodeValue;
                    }
                    break;
            }
        }
        return $content;
    }
}