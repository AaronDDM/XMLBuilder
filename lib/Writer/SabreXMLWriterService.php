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

use AaronDDM\XMLBuilder\Exception\SabreXMLWriterServiceException;
use Sabre\XML;
use Sabre\Xml\Writer;

/**
 * Class SabreXMLWriterService
 * @package AaronDDM\XMLBuilder\Writer
 */
class SabreXMLWriterService extends AbstractWriter
{
    /**
     * @var null|Writer
     */
    protected $xmlWriter;

    /**
     * XMLWriterService constructor.
     * @param null $xmlWriter
     * @throws SabreXMLWriterServiceException
     */
    public function __construct($xmlWriter = null)
    {
        // Ensure we have lib-libxml installed
        if (!class_exists(Writer::class)) {
            throw new SabreXMLWriterServiceException('The Sabre XML Library must be installed in order to use the SabreXMLWriterService.');
        }

        $this->xmlWriter = ($xmlWriter instanceof Writer) ? $xmlWriter : null;
    }

    /**
     * Get's the XML output for our array.
     *
     * @param null $xmlWriter
     * @return string
     * @throws SabreXMLWriterServiceException
     */
    public function getXML($xmlArray): string
    {
        // Only continue if we have anything to generate
        if (empty($xmlArray)) {
            throw new SabreXMLWriterServiceException('You are calling getXML() before building the array.');
        }

        // Get our xml writer
        $xmlWriter = $this->xmlWriter;

        // Start our writer if we don't already have one
        if ($xmlWriter === null) {
            $xmlWriter = new Writer();
            $xmlWriter->openMemory();
            $xmlWriter->setIndent(true);
            $xmlWriter->setIndentString('    ');
            $xmlWriter->startDocument('1.0', 'UTF-8');
        } else {
            $xmlWriter->openMemory();
        }

        $xmlWriter->write($xmlArray);

        return $xmlWriter->outputMemory();
    }
}