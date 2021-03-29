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

use AaronDDM\XMLBuilder\Writer\XMLWriterService;
use AaronDDM\XMLBuilder\XMLBuilder;
use PHPUnit\Framework\TestCase;

class XMLBuilderTest extends TestCase
{
    public function testGetXML()
    {
        $xmlWriterService = new XMLWriterService();
        $xmlBuilder = new XMLBuilder($xmlWriterService);

        $xmlBuilder
            ->createXMLArray()
                ->start('Root')
                    ->addCData('1 First Child First Element', 'This is a test')
                    ->add('First Child Second Element', 'False')
                    ->start('Second Parent')
                        ->add('Second child 1', null, ['myAttr' => 'Attr Value'])
                        ->add('Second child 2', 'False')
                        ->start('Third Parent')
                            ->add('Child')
                        ->end()
                    ->end()
                    ->add('First Child Third Element')
                ->end()
        ;

        $expectedXML = '<?xml version="1.0" encoding="UTF-8"?>
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
';

        $this->assertEquals($expectedXML, $xmlBuilder->getXML());

    }

    public function testGetXMLWithCustomDocType()
    {
        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->setIndent(true);
        $xmlWriter->setIndentString('    ');
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->writeDtd('html', '-//W3C//DTD XHTML 1.0 Transitional//EN', 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd');

        $xmlWriterService = new XMLWriterService($xmlWriter);
        $xmlBuilder = new XMLBuilder($xmlWriterService);

        $xmlBuilder
            ->createXMLArray()
                ->start('Root')
                    ->addCData('1 First Child First Element', 'This is a test')
                    ->add('First Child Second Element', 'False')
                    ->start('Second Parent')
                        ->add('Second child 1', null, ['myAttr' => 'Attr Value'])
                        ->add('Second child 2', 'False')
                        ->start('Third Parent')
                            ->add('Child')
                        ->end()
                    ->end()
                    ->add('First Child Third Element')
                ->end()
        ;

        $expectedXML = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
';

        $this->assertEquals($expectedXML, $xmlBuilder->getXML());

    }
}