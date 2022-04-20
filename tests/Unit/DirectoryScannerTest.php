<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\tests\Unit;

use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use PHPUnit\Framework\TestCase;

final class DirectoryScannerTest extends TestCase
{
    /**
     * Test case that provided directory does not exist.
     */
    public function testScanNotExistingDirectory(): void
    {
        $scanner = new DirectoryScanner('DirectoryScannerTest.php', 'not_existing_path');
        $this->assertEmpty($scanner->getFilePaths());
    }

    /**
     * Test case that provided file does not exist.
     */
    public function testScanFileNameNotSet(): void
    {
        $scanner = new DirectoryScanner('not_existing_file', \dirname(__DIR__));
        $this->assertEmpty($scanner->getFilePaths());
    }

    /**
     * Test success case.
     */
    public function testScanForFilesSuccess(): void
    {
        $scanner = new DirectoryScanner('DirectoryScannerTest.php', dirname(__DIR__));
        $this->assertCount(
            1,
            $scanner->getFilePaths()
        );
    }
}
