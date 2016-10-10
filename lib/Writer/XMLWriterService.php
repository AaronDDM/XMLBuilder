<?php
/*
 * This file is part of the XML Builder Library.
 *
 * (c) Aaron de Mello <https://aaron.de-mello.org/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AaronDDM\XMLBuilder\Writer;

use AaronDDM\XMLBuilder\Exception\XMLWriterServiceException;

/**
 * Class XMLWriterService
 * @package AaronDDM\XMLBuilder\Writer
 */
class XMLWriterService extends AbstractWriter
{
    /**
     * @var null|\XMLWriter
     */
    protected $xmlWriter;

    /**
     * XMLWriterService constructor.
     * @param null $xmlWriter
     * @throws XMLWriterServiceException
     */
    public function __construct($xmlWriter = null)
    {
        // Ensure we have lib-libxml installed
        if (!extension_loaded('libxml')) {
            throw new XMLWriterServiceException('The php extension libxml must be installed in order to use the XMLWriterService.');
        }

        $this->xmlWriter = ($xmlWriter instanceof \XMLWriter) ? $xmlWriter : null;
    }

    /**
     * Get's the XML output for our array.
     *
     * @param null $xmlWriter
     * @return string
     * @throws XMLWriterServiceException
     */
    public function getXML($xmlArray): string
    {
        // Only continue if we have anything to generate
        if (empty($xmlArray)) {
            throw new XMLWriterServiceException('You are calling getXML() before building the array.');
        }

        // Get our xml writer
        $xmlWriter = $this->xmlWriter;

        // Start our writer if we don't already have one
        if ($xmlWriter === null) {
            $xmlWriter = new \XMLWriter();
            $xmlWriter->openMemory();
            $xmlWriter->setIndent(true);
            $xmlWriter->setIndentString('    ');
            $xmlWriter->startDocument('1.0', 'UTF-8');
        } else {
            $xmlWriter->openMemory();
        }

        $this->buildXML([$xmlArray], $xmlWriter);

        return $xmlWriter->outputMemory(true);
    }

    /**
     * @param $array
     * @param \XMLWriter $xmlWriter
     */
    protected function buildXML($array, \XMLWriter &$xmlWriter)
    {
        foreach ($array as $element) {
            $xmlWriter->startElement($element['name']);
            foreach ($element['attributes'] as $attributeName => $value) {
                $xmlWriter->writeAttribute($attributeName, $value);
            }
            if (is_array($element['value'])) {
                $this->buildXML($element['value'], $xmlWriter);
            } else {
                if ($element['value'] !== null) {
                    switch (strtolower($element['type'])) {
                        case 'cdata':
                            $xmlWriter->writeCdata($element['value']);
                            break;
                            break;
                        case 'comment':
                            $xmlWriter->writeComment($element['value']);
                            break;
                        default:
                            $xmlWriter->writeRaw($element['value']);
                    }
                }
            }
            $xmlWriter->endElement();
        }
    }
}