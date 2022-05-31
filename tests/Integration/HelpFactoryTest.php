<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\tests\Integration;

use OxidEsales\EshopIdeHelper\HelpFactory;
use OxidEsales\EshopIdeHelper\Generator;
use PHPUnit\Framework\TestCase;

final class HelpFactoryTest extends TestCase
{
    /**
     * Verify that generator can be constructed.
     */
    public function testGetGenerator(): void
    {
        $helper = new HelpFactory();
        $this->assertTrue(is_a($helper->getGenerator(), Generator::class));
    }
}
