<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper\tests\Unit;

use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;

class DirectoryScannerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test case that provided directory does not exist.
     */
    public function testScanNotExistingDirectory()
    {
        $scanner = new DirectoryScanner('DirectoryScannerTest.php', 'not_existing_path');
        $this->assertEmpty($scanner->getFilePaths());
    }

    /**
     * Test case that provided file does not exist.
     */
    public function testScanFileNameNotSet()
    {
        $scanner = new DirectoryScanner('not_existing_file', dirname(__DIR__));
        $this->assertEmpty($scanner->getFilePaths());
    }

    /**
     * Test success case.
     */
    public function testScanForFilesSuccess()
    {
        $scanner = new DirectoryScanner('DirectoryScannerTest.php', dirname(__DIR__));
        $this->assertEquals(1, count($scanner->getFilePaths()));
    }
}
