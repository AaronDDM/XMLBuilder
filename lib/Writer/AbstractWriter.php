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

/**
 * Class AbstractWriter
 * @package AaronDDM\XMLBuilder\Writer
 */
abstract class AbstractWriter
{
    /**
     * Get's the XML output for our array.
     *
     * @param null $xmlWriter
     * @return string
     */
    public abstract function getXML($xmlArray): string;
}