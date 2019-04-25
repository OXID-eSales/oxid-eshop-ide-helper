<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper\tests\Integration;

use OxidEsales\EshopIdeHelper\HelpFactory;
use OxidEsales\EshopIdeHelper\Generator;

/**
 * Class HelpFactoryTest
 *
 * @package OxidEsales\EshopIdeHelper\tests\Integration
 */
class HelpFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Verify that generator can be constructed.
     */
    public function testGetGenerator()
    {
        $helper = new HelpFactory();
        $this->assertTrue(is_a($helper->getGenerator(), Generator::class));
    }
}
