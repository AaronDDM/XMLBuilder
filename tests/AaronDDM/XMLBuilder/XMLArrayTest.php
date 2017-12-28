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
    public function testSingleElementArray()
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

    }

    public function testSingleElementWithAttributes()
    {
        $xmlArray = XMLArray::initiate()
            ->start('Root', ['myAttr' => 'attr-value'])
            ->addCData('Test', 'test')
            ->end();;

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
    }

    public function testNestedChildrenArray()
    {
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

    public function testArrayLoops()
    {
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

        $xmlArray = XMLArray::initiate()
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

        $expectedArray = [
            'name' => 'Root',
            'value' => [
                [
                    'name' => 'Users',
                    'value' => [
                        [
                            'name' => 'User',
                            'value' => [
                                [
                                    'name' => 'name',
                                    'value' => 'John Doe',
                                    'attributes' => [],
                                    'type' => null
                                ],
                                [
                                    'name' => 'age',
                                    'value' => 32,
                                    'attributes' => [],
                                    'type' => null
                                ]
                            ],
                            'attributes' => [],
                            'type' => null
                        ],
                        [
                            'name' => 'User',
                            'value' => [
                                [
                                    'name' => 'name',
                                    'value' => 'Jane Doe',
                                    'attributes' => [],
                                    'type' => null
                                ],
                                [
                                    'name' => 'age',
                                    'value' => 98,
                                    'attributes' => [],
                                    'type' => null
                                ]
                            ],
                            'attributes' => [],
                            'type' => null
                        ]
                    ],
                    'attributes' => [],
                    'type' => null
                ]
            ],
            'attributes' => [],
            'type' => null
        ];

        $this->assertEquals($expectedArray, $xmlArray->getArray());
    }
}