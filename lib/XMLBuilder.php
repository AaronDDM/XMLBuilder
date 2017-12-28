<?php
declare(strict_types=1);
/*
 * This file is part of the XML Builder Library.
 *
 * (c) Aaron de Mello <https://aaron.de-mello.org/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AaronDDM\XMLBuilder;

use AaronDDM\XMLBuilder\Exception\XMLBuilderException;
use AaronDDM\XMLBuilder\Writer\AbstractWriter;

/**
 * Class XMLBuilder
 * @package AaronDDM\XMLBuilder
 */
class XMLBuilder
{
    /**
     * @var AbstractWriter
     */
    protected $writer;

    /**
     * @var XMLArray
     */
    protected $xmlArray;

    /**
     * @var null|string
     */
    protected $elementDataClass = XMLElementData::class;

    /**
     * XMLBuilder constructor.
     * @param AbstractWriter $writer
     */
    public function __construct(AbstractWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @return XMLArray
     */
    public function createXMLArray()
    {
        return $this->xmlArray = XMLArray::initiate($this->getElementDataClass());
    }

    /**
     * @return AbstractWriter
     */
    public function getWriter(): AbstractWriter
    {
        return $this->writer;
    }

    /**
     * @param AbstractWriter $writer
     * @return XMLBuilder
     */
    public function setWriter(AbstractWriter $writer): XMLBuilder
    {
        $this->writer = $writer;
        return $this;
    }

    /**
     * @return XMLArray
     */
    public function getXMLArray(): XMLArray
    {
        return $this->xmlArray;
    }

    /**
     * @return null|string
     */
    public function getElementDataClass(): ?string
    {
        return $this->elementDataClass;
    }

    /**
     * @param null|string $elementDataClass
     * @return XMLBuilder
     */
    public function setElementDataClass(?string $elementDataClass): XMLBuilder
    {
        $this->elementDataClass = $elementDataClass;
        return $this;
    }

    /**
     * @return string
     * @throws XMLBuilderException
     */
    public function getXML(): string
    {
        if (!$this->getXMLArray() instanceof XMLArray) {
            throw new XMLBuilderException('You attempted to call getXML() without first calling createXMLArray().');
        }

        return $this->writer->getXML($this->getXMLArray()->getArray());
    }
}