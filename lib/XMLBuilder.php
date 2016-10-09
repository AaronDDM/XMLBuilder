<?php
/*
 * This file is part of the XML Builder Library.
 *
 * (c) Aaron de Mello <https://aaron.de-mello.org/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AaronDDM\XMLBuilder;

use AaronDDM\XMLBuilder\Writer\AbstractWriter;

/**
 * Class XMLBuilder
 * @package AaronDDM\XMLBuilder
 */
class XMLBuilder extends XMLArray
{
    /**
     * @var AbstractWriter
     */
    protected $writer;

    /**
     * XMLBuilder constructor.
     * @param AbstractWriter $writer
     */
    public function __construct(AbstractWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Get's the XML output for our array.
     *
     * @param null $xmlWriter
     * @return string
     */
    public function getXML(): string
    {
        return $this->writer->getXML($this->getArray());
    }

}