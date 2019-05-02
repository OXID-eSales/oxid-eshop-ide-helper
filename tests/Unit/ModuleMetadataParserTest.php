<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper\tests\Unit;

use \Webmozart\PathUtil\Path;
use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;

class ModuleMetadataParserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Success case.
     */
    public function testGetExtendedClasses()
    {
        $testData = [
            Path::join([__DIR__, 'testData', 'example_1.php']),
            Path::join([__DIR__, 'testData', 'example_2.php']),
            Path::join([__DIR__, 'testData', 'example_3.php']),
        ];

        $scanner = $this->getMockBuilder(DirectoryScanner::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilePaths'])
            ->getMock();
        $scanner->expects($this->any())
            ->method('getFilePaths')
            ->will($this->returnValue($testData));

        $parser = new ModuleMetadataParser($scanner);

        $expected = [
            'OxidEsales\TestModule\Core\Header'        => 'OxidEsales\Eshop\Core\Header',
            'OxidEsales\TestModule\Core\ShopControl'   => 'OxidEsales\Eshop\Core\ShopControl',
            'OxidEsales\TestModule\Core\WidgetControl' => 'OxidEsales\Eshop\Core\WidgetControl',
            'nonamespace_testmodule_header'            => 'OxidEsales\Eshop\Core\Header',
        ];
        $this->assertEquals($expected , $parser->getChainExtendedClasses());
    }

    /**
     * Success case.
     */
    public function testNoClasses()
    {
        $testData = [
            Path::join([__DIR__, 'testData', 'example_2.php']),
        ];

        $scanner = $this->getMockBuilder(DirectoryScanner::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilePaths'])
            ->getMock();
        $scanner->expects($this->any())
            ->method('getFilePaths')
            ->will($this->returnValue($testData));

        $parser = new ModuleMetadataParser($scanner);

        $this->assertEquals([], $parser->getChainExtendedClasses());
    }
}
