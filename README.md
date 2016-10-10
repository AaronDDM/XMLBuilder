# PHP XML Builder Library

## Installation

```
composer require aaronddm/xml-builder
```

### Prerequisites

- PHP >=7.0.0

### Example: Using XMLWriter
```php
<?php

require_once 'vendor/autoload.php';

use AaronDDM\XMLBuilder\XMLBuilder;
use AaronDDM\XMLBuilder\Writer\XMLWriterService;

$xmlWriterService = new XMLWriterService();
$xmlBuilder = new XMLBuilder($xmlWriterService);

$xmlBuilder->setParseValueFunction(function ($value, $type) {
    if(is_bool($value)) {
        $type = 'boolean';
    }

    switch($type) {
        case 'boolean':
            $value = ($value) ? 'True' : 'False';
        break;
    }

    return $value;
});

$xmlBuilder
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
    ->end()
;

var_dump($xmlBuilder->getXML());
```


#### Output
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