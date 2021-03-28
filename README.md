# PHP XML Builder Library
This is a simple PHP 7.1+ based XML Builder library. Use it to easily generate XML output with just PHP.

[![Build Status](https://travis-ci.org/AaronDDM/XMLBuilder.svg?branch=master)](https://travis-ci.org/AaronDDM/XMLBuilder)

## Table of Contents

- [PHP XML Builder Library](#php-xml-builder-library)
  - [Table of Contents](#table-of-contents)
  - [Installation](#installation)
  - [Prerequisites](#prerequisites)
  - [Basic Usage](#basic-usage)
    - [Example: Using XMLWriter](#example-using-xmlwriter)
    - [Output](#output)
  - [Looping through data](#looping-through-data)
    - [Example: XML output of a list of users](#example-xml-output-of-a-list-of-users)
    - [Output](#output-1)
  - [Using a custom "XMLElementData" class](#using-a-custom-xmlelementdata-class)
    - [Example: Customized MyXMLElementData class](#example-customized-myxmlelementdata-class)
    - [Output](#output-2)
  - [Running tests](#running-tests)
  - [License](#license)


## Installation

```
composer require aaronddm/xml-builder
```

## Prerequisites

- PHP >=7.2.0
- php-xml if using XMLWriter

## Basic Usage
The following is an example of the most basic usage of this library.

### Example: Using XMLWriter
```php
<?php

require_once 'vendor/autoload.php';

use AaronDDM\XMLBuilder\XMLBuilder;
use AaronDDM\XMLBuilder\Writer\XMLWriterService;
use AaronDDM\XMLBuilder\Exception\XMLArrayException;

$xmlWriterService = new XMLWriterService();
$xmlBuilder = new XMLBuilder($xmlWriterService);

try {
    $xmlBuilder
        ->createXMLArray()
            ->start('Root')
                ->addCData('1 First Child First Element', 'This is a test')
                ->add('First Child Second Element', false)
                ->start('Second Parent')
                    ->add('Second child 1', null, ['myAttr' => 'Attr Value'])
                    ->add('Second child 2', false)
                    ->start('Third Parent')
                        ->add('Child')
                    ->end()
                ->end()
                ->add('First Child Third Element')
            ->end();

    var_dump($xmlBuilder->getXML());
} catch (XMLArrayException $e) {
    var_dump('An exception occurred: ' . $e->getMessage());
}
```

### Output
```
string(414) "<?xml version="1.0" encoding="UTF-8"?>
<Root>
    <FirstChildFirstElement><![CDATA[This is a test]]></FirstChildFirstElement>
    <FirstChildSecondElement>False</FirstChildSecondElement>
    <SecondParent>
        <Secondchild myAttr="Attr Value"/>
        <Secondchild>False</Secondchild>
        <ThirdParent>
            <Child/>
        </ThirdParent>
    </SecondParent>
    <FirstChildThirdElement/>
</Root>
"
```

## Looping through data
You easily added sets of data using the startLoop method provided.

### Example: XML output of a list of users
```php
<?php
require_once 'vendor/autoload.php';

use AaronDDM\XMLBuilder\XMLArray;
use AaronDDM\XMLBuilder\XMLBuilder;
use AaronDDM\XMLBuilder\Writer\XMLWriterService;

$users = [
    [
        'name' => 'John Doe',
        'age' => 32
    ],
    [
        'name' => 'Jane Doe',
        'age' => 98
    ]
];


$xmlWriterService = new XMLWriterService();
$xmlBuilder = new XMLBuilder($xmlWriterService);

$xmlBuilder
    ->createXMLArray()
        ->start('Root')
            ->startLoop('Users', [], function (XMLArray $XMLArray) use ($users) {
                foreach ($users as $user) {
                    $XMLArray->start('User')
                        ->add('name', $user['name'])
                        ->add('age', $user['age']);
                }
            })
            ->end()
        ->end();

var_dump($xmlBuilder->getXML());
```

### Output
```
string(261) "<?xml version="1.0" encoding="UTF-8"?>
<Root>
    <Users>
        <User>
            <name>John Doe</name>
            <age>32</age>
        </User>
        <User>
            <name>Jane Doe</name>
            <age>98</age>
        </User>
    </Users>
</Root>
"
```

## Using a custom "XMLElementData" class
You can override the XMLElementData element class to implement transformations to the value of your data based on the type passed.
To do this, you simply extend the XMLElementData class and override any of the methods to your liking.

### Example: Customized MyXMLElementData class
```php
<?php

require_once 'vendor/autoload.php';

use AaronDDM\XMLBuilder\XMLElementData;
use AaronDDM\XMLBuilder\XMLBuilder;
use AaronDDM\XMLBuilder\Writer\XMLWriterService;
use AaronDDM\XMLBuilder\Exception\XMLArrayException;

/**
 * Class MyXMLElementData
 */
class MyXMLElementData extends XMLElementData
{
    /**
     * @return mixed
     */
    public function getValue()
    {
        $type = $this->getType();
        $value = $this->value;

        if(is_bool($value)) {
            $type = 'boolean';
        }

        switch($type) {
            case 'specialType':
                $value = 'Special Type Value';
                break;
            case 'boolean':
                $value = ($value) ? 'True' : 'False';
                break;
        }

        return $value;
    }
}

$xmlWriterService = new XMLWriterService();
$xmlBuilder = new XMLBuilder($xmlWriterService);
$xmlBuilder->setElementDataClass(MyXMLElementData::class);

try {
    $xmlBuilder
        ->createXMLArray()
            ->start('Root')
                ->addCData('1 First Child First Element', 'This is a test')
                ->add('First Child Second Element', false)
                ->start('Second Parent')
                    ->add('Second child 1', null, ['myAttr' => 'Attr Value'])
                    ->add('Second child 2', false)
                    ->start('Third Parent')
                        ->add('Child')
                        ->add('Special Type Child', "1", [], 'specialType')
                    ->end()
                ->end()
                ->add('First Child Third Element')
            ->end();

    var_dump($xmlBuilder->getXML());
} catch (XMLArrayException $e) {
    var_dump('An exception occurred: ' . $e->getMessage());
}
```

### Output
```
string(482) "<?xml version="1.0" encoding="UTF-8"?>
<Root>
    <FirstChildFirstElement><![CDATA[This is a test]]></FirstChildFirstElement>
    <FirstChildSecondElement>False</FirstChildSecondElement>
    <SecondParent>
        <Secondchild myAttr="Attr Value"/>
        <Secondchild>False</Secondchild>
        <ThirdParent>
            <Child/>
            <SpecialTypeChild>Special Type Value</SpecialTypeChild>
        </ThirdParent>
    </SecondParent>
    <FirstChildThirdElement/>
</Root>
"
```

## Running tests
```bash
cd /root/of/project/
vendor/bin/phpunit
```

OR

```bash
docker build -t xmlbuilder .
docker run -u appuser -it --rm xmlbuilder vendor/bin/phpunit
```

## License
This project is open-sourced software licensed under the [MIT](https://opensource.org/licenses/MIT) license.
