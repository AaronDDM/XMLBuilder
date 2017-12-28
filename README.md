# PHP XML Builder Library

## Installation

```
composer require aaronddm/xml-builder
```

## Prerequisites

- PHP >=7.1.0
- php-xml if using XMLWriter

## Example: Using XMLWriter
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
```
cd /root/of/project/
vendor/bin/phpunit
```