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

use AaronDDM\XMLBuilder\Exception\XMLBuilderException;

/**
 * Class XMLBuilder
 * @package AaronDDM\XMLBuilder
 */
class XMLBuilder
{
    /**
     * @var array
     */
    protected $array = [];

    /**
     * @var array
     */
    protected $stack = [];

    /**
     * @var bool
     */
    protected $opened = null;

    /**
     * @var \Closure
     */
    protected $parseValueFunction;

    /**
     * Starts a new parent element in the stack.
     *
     * @param $name
     * @param null $attributes
     * @return XMLBuilder
     */
    public function start($name, $attributes = null): XMLBuilder
    {
        $newArray = $this->createArray($name, [], $attributes);

        $this->stack[] = $newArray;

        return $this;
    }

    /**
     * Adds a single/child element to the stack.
     *
     * @param $name
     * @param null $value
     * @param array $attributes
     * @param mixed $type
     * @return XMLBuilder
     * @throws XMLBuilderException
     */
    public function add($name, $value = null, $attributes = [], $type = null): XMLBuilder
    {
        if (empty($this->stack)) {
            throw new XMLBuilderException('You must start a root element before adding new elements.');
        }

        $value = $this->parseValue($value, $type);

        $newArray = $this->createArray($name, $value, $attributes);

        $this->addToStack($newArray);

        return $this;
    }

    /**
     * Marks that the previous opened element has ended.
     *
     * @return XMLBuilder
     * @throws XMLBuilderException
     */
    public function end(): XMLBuilder
    {
        if (empty($this->stack)) {
            throw new XMLBuilderException('No elements in the stack to call end().');
        }

        $lastElement = array_pop($this->stack);

        if (!empty($this->stack)) {
            $this->addToStack($lastElement);
        } else {
            $this->array = $lastElement;
        }

        return $this;
    }

    /**
     * Parse value based on the type usually.
     *
     * @param $value
     * @param $type
     * @return mixed
     */
    public function parseValue($value, $type)
    {
        $parserFunction = $this->getParseValueFunction();
        return ($this->getParseValueFunction() instanceof \Closure) ? $parserFunction($value, $type) : $value;
    }

    /**
     * Get's the current stack
     *
     * @return array
     */
    public function getStack(): array
    {
        return $this->stack;
    }

    /**
     * @param array $stack
     * @return XMLBuilder
     */
    public function setStack(array $stack): XMLBuilder
    {
        $this->stack = $stack;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOpened(): bool
    {
        return $this->opened;
    }

    /**
     * @param boolean $opened
     * @return XMLBuilder
     */
    public function setOpened(bool $opened): XMLBuilder
    {
        $this->opened = $opened;
        return $this;
    }

    /**
     * @return \Closure
     */
    public function getParseValueFunction()
    {
        return $this->parseValueFunction;
    }

    /**
     * @param \Closure $parseValueFunction
     * @return XMLBuilder
     */
    public function setParseValueFunction($parseValueFunction)
    {
        $this->parseValueFunction = $parseValueFunction;
        return $this;
    }

    /**
     * Gets our generates our array that will be used
     * to create our XML output.
     *
     * @return array
     * @throws XMLBuilderException
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * Get's the XML output for our array.
     *
     * @param null $xmlWriter
     * @return string
     * @throws XMLBuilderException
     */
    public function getXML($xmlWriter = null): string
    {
        // Only continue if we have anything to generate
        $xmlArray = $this->getArray();
        if(empty($xmlArray)) {
            throw new XMLBuilderException('You are calling getXML() before building the array.');
        }

        // Start our writer if we don't already have one
        if($xmlWriter === null) {
            $xmlWriter = new \XMLWriter();
            $xmlWriter->openMemory();
            $xmlWriter->setIndent(true);
            $xmlWriter->setIndentString('    ');
            $xmlWriter->startDocument('1.0', 'UTF-8');
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
        foreach($array as $element)
        {
            $xmlWriter->startElement($element['name']);
            if(is_array($element['value'])) {
                $this->buildXML($element['value'], $xmlWriter);
            } else {
                $xmlWriter->writeCdata($element['value']);
            }
            $xmlWriter->endElement();
        }
    }

    /**
     * Adds a given array to the stack.
     *
     * @param $array
     * @return XMLBuilder
     */
    protected function addToStack($array): XMLBuilder
    {
        $lastElementKey = key(array_slice($this->stack, -1, 1, TRUE));
        $parentElement = (isset($this->stack[$lastElementKey])) ? $this->stack[$lastElementKey] : false;

        if ($parentElement !== false) {
            $parentElement['value'][] = $array;

            $this->stack[$lastElementKey] = $parentElement;
        }

        return $this;
    }

    /**
     * Generates an array that we'll use to build our
     * array that converts to our XML output.
     *
     * @param string $name
     * @param mixed $value
     * @param array $attributes
     * @return array
     */
    protected function createArray($name, $value, $attributes = []): array
    {
        $name = preg_replace('/([^a-zA-Z]+)/', '', $name);
        return ['name' => $name, 'value' => $value, 'attributes' => $attributes];
    }
}