<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\tests\Unit;

use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use PHPUnit\Framework\TestCase;

final class ModuleExtendClassMapProviderTest extends TestCase
{
    /**
     * Success case.
     */
    public function testGetClassMap(): void
    {
        $testData = [
            'OxidEsales\TestModule\Core\Header'        => 'OxidEsales\Eshop\Core\Header',
            'OxidEsales\TestModule\Core\ShopControl'   => 'OxidEsales\Eshop\Core\ShopControl',
            'nonamespace_testmodule_header'            => 'OxidEsales\Eshop\Core\Header',
        ];

        $parser = $this->getMockBuilder(ModuleMetadataParser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getChainExtendedClasses'])
            ->getMock();
        $parser
            ->method('getChainExtendedClasses')
            ->willReturn($testData);

        $classMap = new ModuleExtendClassMapProvider($parser);

        $expected = [
            [
                'isAbstract'      => false,
                'isInterface'     => false,
                'childClassName'  => 'Header_parent',
                'parentClassName' => 'OxidEsales\\Eshop\\Core\\Header',
                'namespace'       => 'OxidEsales\\TestModule\\Core',
            ],
            [
                'isAbstract'      => false,
                'isInterface'     => false,
                'childClassName'  => 'ShopControl_parent',
                'parentClassName' => 'OxidEsales\\Eshop\\Core\\ShopControl',
                'namespace'       => 'OxidEsales\\TestModule\\Core',
            ],
            [
                'isAbstract'      => false,
                'isInterface'     => false,
                'childClassName'  => 'nonamespace_testmodule_header_parent',
                'parentClassName' => 'OxidEsales\\Eshop\\Core\\Header',
                'namespace'       => '',
            ]
        ];

        $this->assertEquals($expected, $classMap->getModuleParentClassMap());
    }
}
