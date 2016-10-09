## PHP XML Builder Library

### Example
```php
<?php

require_once 'vendor/autoload.php';

use AaronDDM\XMLBuilder\XMLBuilder;
use AaronDDM\XMLBuilder\Writer\XMLWriterService;

$xmlWriterService = new XMLWriterService();
$xmlBuilder = new XMLBuilder($xmlWriterService);

$xmlBuilder->setParseValueFunction(function ($value, $type) {
    if(is_bool($value)) {
        $type = 'bool';
    }

    switch($type) {
        case 'bool':
            $value = ($value) ? 'True' : 'False';
        break;
    }

    return $value;
});

$xmlBuilder
    ->start('Root')
        ->add('First Child First Element', 'This is a test')
        ->add('First Child Second Element', false)
        ->start('Second Parent')
            ->add('Second child 1')
            ->add('Second child 2')
            ->start('Third Parent')
                ->add('Child')
            ->end()
        ->end()
        ->add('First Child Third Element')
    ->end()
;

var_dump($xmlBuilder->getXML());

/*
Output:


string(493) "<?xml version="1.0" encoding="UTF-8"?>
<Root>
    <FirstChildFirstElement><![CDATA[This is a test]]></FirstChildFirstElement>
    <FirstChildSecondElement><![CDATA[False]]></FirstChildSecondElement>
    <SecondParent>
        <Secondchild><![CDATA[]]></Secondchild>
        <Secondchild><![CDATA[]]></Secondchild>
        <ThirdParent>
            <Child><![CDATA[]]></Child>
        </ThirdParent>
    </SecondParent>
    <FirstChildThirdElement><![CDATA[]]></FirstChildThirdElement>
</Root>
"
*/