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

/**
 * Class XMLElementData
 * @package AaronDDM\XMLBuilder
 */
class XMLElementData
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var null|object|string|integer|float|double|boolean
     */
    protected $value;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param string $name
     * @param null|object|string|integer|float|double|boolean $value
     * @param array $attributes
     * @param null|string $type
     * @return XMLElementData
     */
    public static function create(string $name, $value = null, $attributes = [], ?string $type = null): XMLElementData
    {
        $arrayObject = new static();
        $arrayObject->setName($name);
        $arrayObject->setValue($value);
        $arrayObject->setType($type);
        $arrayObject->setAttributes($attributes);

        return $arrayObject;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return XMLElementData
     */
    public function setName(string $name): XMLElementData
    {
        $name = preg_replace('/([^a-zA-Z]+)/', '', $name);

        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     * @return XMLElementData
     */
    public function setType(?string $type): XMLElementData
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return XMLElementData
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return XMLElementData
     */
    public function setAttributes(array $attributes): XMLElementData
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'value' => ($this->getValue() instanceof XMLArray) ? $this->getValue()->getArray() : $this->getValue(),
            'attributes' => $this->getAttributes()
        ];
    }
}