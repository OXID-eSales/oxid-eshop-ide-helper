<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper\tests\Unit;

use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;

class ModuleExtendClassMapProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Success case.
     */
    public function testGetClassMap()
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
        $parser->expects($this->any())
            ->method('getChainExtendedClasses')
            ->will($this->returnValue($testData));

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

        $this->assertEquals($expected , $classMap->getModuleParentClassMap());
    }
}
