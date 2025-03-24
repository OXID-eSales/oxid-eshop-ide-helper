<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\tests\Unit;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use PHPUnit\Framework\TestCase;

final class DirectoryScannerTest extends TestCase
{
    private vfsStreamDirectory $root;
    private string $rootPath;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup();
        $this->rootPath = $this->root->url();
    }

    /**
     * Test case that provided directory does not exist.
     */
    public function testScanNotExistingDirectory(): void
    {
        $scanner = new DirectoryScanner(uniqid(), $this->rootPath . uniqid());
        $this->assertEmpty($scanner->getFilePaths());
    }

    /**
     * Test case that provided file does not exist.
     */
    public function testScanFileNameNotSet(): void
    {
        $scanner = new DirectoryScanner(uniqid(), $this->rootPath);
        $this->assertEmpty($scanner->getFilePaths());
    }

    /**
     * Test success case.
     */
    public function testScanForFilesSuccess(): void
    {
        $fileName = uniqid();
        vfsStream::newFile($fileName)->at($this->root);

        $scanner = new DirectoryScanner($fileName, $this->rootPath);
        $this->assertCount(1, $scanner->getFilePaths());
    }

    public function testHiddenDirectoriesAreSkipped(): void
    {
        $fileName = uniqid();
        $hiddenDir = vfsStream::newDirectory('.' . uniqid())->at($this->root);

        vfsStream::newFile($fileName)->at($hiddenDir);

        $scanner = new DirectoryScanner($fileName, $this->rootPath);
        $this->assertEmpty($scanner->getFilePaths());
    }

    public function testHiddenFilesAreSkipped(): void
    {
        $HiddenFileName = '.' . uniqid();
        vfsStream::newFile($HiddenFileName)->at($this->root);

        $scanner = new DirectoryScanner($HiddenFileName, $this->rootPath);
        $this->assertEmpty($scanner->getFilePaths());
    }
}
