## PHP XML Builder Library

### Example
```php
require_once 'vendor/autoload.php';

use AaronDDM\XMLBuilder\XMLBuilder;

$xmlBuilder = new XMLBuilder();

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