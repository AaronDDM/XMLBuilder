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

use AaronDDM\XMLBuilder\Exception\XMLArrayException;

/**
 * Class XMLArray
 * @package AaronDDM\XMLArray
 */
class XMLArray
{
    /**
     * @var XMLArray
     */
    protected $parent;

    /**
     * @var array
     */
    protected $stack = [];

    /**
     * @var null|string
     */
    protected $elementDataClass = XMLElementData::class;

    /**
     * @param null|string $elementDataClass
     * @return XMLArray
     */
    public static function initiate(?string $elementDataClass = null)
    {
        if (is_null($elementDataClass)) {
            $elementDataClass = XMLElementData::class;
        }

        $xmlArray = new static();
        $xmlArray->setElementDataClass($elementDataClass);

        return $xmlArray;
    }

    /**
     * @param string $rootName
     * @param array $attributes
     * @return XMLArray
     */
    public function start(string $rootName, array $attributes = []): XMLArray
    {
        $root = self::initiate($this->getElementDataClass());
        $root->parent = $this;

        $this->stack[] = $this->getElementDataClass()::create($rootName, $root, $attributes);

        return $root;
    }

    /**
     * @param string $name
     * @param null|object|string|integer|float|double|boolean $value
     * @param array $attributes
     * @param null|string $type
     * @return XMLArray
     * @throws XMLArrayException
     */
    public function add(string $name, $value = null, array $attributes = [], ?string $type = null): XMLArray
    {
        if (!$this->hasRoot()) {
            throw new XMLArrayException('No root found. You must call start() before calling add()');
        }

        $this->stack[] = $this->getElementDataClass()::create($name, $value, $attributes, $type);
        return $this;
    }

    /**s
     * @param string $name
     * @param array $attributes
     * @param \Closure $loopFunction
     * @return mixed
     */
    public function startLoop(string $name, array $attributes = [], \Closure $loopFunction)
    {
        $xmlArray = $this->start($name, $attributes);

        $loop = $loopFunction($xmlArray);

        return $xmlArray;
    }    

    /**
     * @param \Closure $loopFunction
     * @return mixed
     */
    public function loop(\Closure $loopFunction)
    {
        $loop = $loopFunction($this);

        return $this;
    }

    /**
     * @param string $name
     * @param null|object|string|integer|float|double|boolean $value
     * @param array $attributes
     * @return XMLArray
     * @throws XMLArrayException
     */
    public function addCData(string $name, $value = null, array $attributes = []): XMLArray
    {
        return $this->add($name, $value, $attributes, 'cdata');
    }

    /**
     * @return XMLArray
     */
    public function end(): XMLArray
    {
        return ($this->hasRoot()) ? $this->parent : $this;
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        $array = [];
        /** @var XMLElementData $root */
        foreach ($this->stack as $root)
        {
            $array[] = $root->getArray();
        }

        return ($this->hasRoot()) ? $array : array_pop($array);
    }

    /**
     * @return string
     */
    public function getElementDataClass(): string
    {
        return $this->elementDataClass;
    }

    /**
     * @param string $elementDataClass
     * @return XMLArray
     */
    public function setElementDataClass(string $elementDataClass): XMLArray
    {
        $this->elementDataClass = $elementDataClass;
        return $this;
    }

    /**
     * @return bool
     */
    protected function hasRoot(): bool
    {
        return (!empty($this->parent));
    }
}
