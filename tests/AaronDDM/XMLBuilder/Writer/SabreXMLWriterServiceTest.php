<?php
/*
 * This file is part of the XML Builder Library.
 *
 * (c) Aaron de Mello <https://aaron.de-mello.org/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AaronDDM\Tests\AaronDDM\XMLBuilder;

use AaronDDM\XMLBuilder\Writer\SabreXMLWriterService;
use PHPUnit\Framework\TestCase;

class SabreXMLWriterServiceTest extends TestCase
{
    public function testGetXML()
    {
        $xmlArray = [
            'name' => 'Root',
            'value' => [
                [
                    'name' => 'Child',
                    'value' => null,
                    'attributes' => ['myAttr' => 'myValue'],
                    'type' => null
                ]
            ],
            'attributes' => [],
            'type' => null
        ];

        $expectedXML = '<?xml version="1.0" encoding="UTF-8"?>
<Root>
    <Child myAttr="myValue"/>
</Root>
';

        $XMLWriterService = new SabreXMLWriterService();

        $actualXML = $XMLWriterService->getXML($xmlArray);

        $this->assertEquals($expectedXML, $actualXML);
    }
}