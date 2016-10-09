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

use AaronDDM\XMLBuilder\Exception\XMLArrayException;

/**
 * Class XMLArray
 * @package AaronDDM\XMLArray
 */
class XMLArray
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
     * @var \Closure
     */
    protected $parseValueFunction;

    /**
     * Starts a new parent element in the stack.
     *
     * @param $name
     * @param null $attributes
     * @return XMLArray
     */
    public function start($name, $attributes = []): XMLArray
    {
        $newArray = $this->createArray($name, [], $attributes);

        $this->stack[] = $newArray;

        return $this;
    }

    /**
     * Add a single/child element with a cdata type.
     *
     * @param $name
     * @param null $value
     * @param array $attributes
     * @return XMLArray
     */
    public function addBoolean($name, $value = null, $attributes = []): XMLArray
    {
        $this->add($name, $value, $attributes, 'boolean');
        return $this;
    }

    /**
     * Add a single/child element with a cdata type.
     *
     * @param $name
     * @param null $value
     * @param array $attributes
     * @return XMLArray
     */
    public function addCData($name, $value = null, $attributes = []): XMLArray
    {
        $this->add($name, $value, $attributes, 'cdata');
        return $this;
    }

    /**
     * Adds a single/child element to the stack.
     *
     * @param $name
     * @param null $value
     * @param array $attributes
     * @param mixed $type
     * @return XMLArray
     * @throws XMLArrayException
     */
    public function add($name, $value = null, $attributes = [], $type = null): XMLArray
    {
        if (empty($this->stack)) {
            throw new XMLArrayException('You must start a root element before adding new elements.');
        }

        $value = $this->parseValue($value, $type);

        $newArray = $this->createArray($name, $value, $attributes, $type);

        $this->addToStack($newArray);

        return $this;
    }

    /**
     * Marks that the previous opened element has ended.
     *
     * @return XMLArray
     * @throws XMLArrayException
     */
    public function end(): XMLArray
    {
        if (empty($this->stack)) {
            throw new XMLArrayException('No elements in the stack to call end().');
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
     * @return XMLArray
     */
    public function setStack(array $stack): XMLArray
    {
        $this->stack = $stack;
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
     * @return XMLArray
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
     * @throws XMLArrayException
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * Adds a given array to the stack.
     *
     * @param $array
     * @return XMLArray
     */
    protected function addToStack($array): XMLArray
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
     * @param array $type
     * @param array $ns
     * @return array
     */
    protected function createArray($name, $value, $attributes = [], $type = null, $ns = null): array
    {
        $name = preg_replace('/([^a-zA-Z]+)/', '', $name);
        return ['name' => $name, 'value' => $value, 'type' => $type, 'attributes' => $attributes];
    }
}