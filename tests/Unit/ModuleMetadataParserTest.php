<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;
use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;

final class ModuleMetadataParserTest extends TestCase
{
    /**
     * Success case.
     */
    public function testGetExtendedClasses(): void
    {
        $testData = [
            Path::join(__DIR__, 'testData', 'example_1.php'),
            Path::join(__DIR__, 'testData', 'example_2.php'),
            Path::join(__DIR__, 'testData', 'example_3.php'),
        ];

        $scanner = $this->getMockBuilder(DirectoryScanner::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilePaths'])
            ->getMock();
        $scanner
            ->method('getFilePaths')
            ->willReturn($testData);

        $parser = new ModuleMetadataParser($scanner);

        $expected = [
            'OxidEsales\TestModule\Core\Header'        => 'OxidEsales\Eshop\Core\Header',
            'OxidEsales\TestModule\Core\ShopControl'   => 'OxidEsales\Eshop\Core\ShopControl',
            'OxidEsales\TestModule\Core\WidgetControl' => 'OxidEsales\Eshop\Core\WidgetControl',
            'nonamespace_testmodule_header'            => 'OxidEsales\Eshop\Core\Header',
        ];
        $this->assertEquals($expected, $parser->getChainExtendedClasses());
    }

    /**
     * Success case.
     */
    public function testNoClasses(): void
    {
        $testData = [
            Path::join(__DIR__, 'testData', 'example_2.php'),
        ];

        $scanner = $this->getMockBuilder(DirectoryScanner::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilePaths'])
            ->getMock();
        $scanner
            ->method('getFilePaths')
            ->willReturn($testData);

        $parser = new ModuleMetadataParser($scanner);

        $this->assertEquals([], $parser->getChainExtendedClasses());
    }
}
