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

use AaronDDM\XMLBuilder\XMLArray;
use PHPUnit\Framework\TestCase;

class XMLArrayTest extends TestCase
{
    public function testGetArray()
    {
        $xmlArray = XMLArray::initiate()
            ->start('Root')
                ->add('Test')
            ->end();

        $expectedArray = [
            'name' => 'Root',
            'value' => [
                [
                    'name' => 'Test',
                    'value' => null,
                    'attributes' => [],
                    'type' => null
                ]
            ],
            'attributes' => [],
            'type' => null
        ];

        $this->assertEquals($expectedArray, $xmlArray->getArray());

        $xmlArray = XMLArray::initiate()
            ->start('Root', ['myAttr' => 'attr-value'])
                ->addCData('Test', 'test')
            ->end();
        ;

        $expectedArray = [
            'name' => 'Root',
            'value' => [
                [
                    'name' => 'Test',
                    'value' => 'test',
                    'attributes' => [],
                    'type' => 'cdata'
                ]
            ],
            'attributes' => ['myAttr' => 'attr-value'],
            'type' => null
        ];

        $this->assertEquals($expectedArray, $xmlArray->getArray());
        $xmlArray = XMLArray::initiate()
            ->start('Root', ['myAttr' => 'attr-value'])
                ->addCData('Test', 'test')
                ->start('Child')
                    ->start('Child')
                    ->end()
                ->end()
            ->end();
        ;

        $expectedArray = [
            'name' => 'Root',
            'value' => [
                [
                    'name' => 'Test',
                    'value' => 'test',
                    'attributes' => [],
                    'type' => 'cdata'
                ],
                [
                    'name' => 'Child',
                    'value' => [
                        [
                            'name' => 'Child',
                            'value' => [],
                            'attributes' => [],
                            'type' => null
                        ]
                    ],
                    'attributes' => [],
                    'type' => null
                ]
            ],
            'attributes' => ['myAttr' => 'attr-value'],
            'type' => null
        ];

        $this->assertEquals($expectedArray, $xmlArray->getArray());
    }
}