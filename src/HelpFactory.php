<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectDirectoriesLocator;
use Symfony\Component\Filesystem\Path;
use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;

/**
 * @deprecated class will be removed in next major
 */
class HelpFactory
{
    private string $scanForFilename = 'metadata.php';
    private string $scanForDirectory = 'modules';
    private ?UnifiedNameSpaceClassMapProvider $unifiedNameSpaceClassMapProvider = null;
    private ?BackwardsCompatibilityClassMapProvider $backwardsCompatibilityClassMapProvider = null;
    private ?ModuleExtendClassMapProvider $moduleExtendClassMapProvider = null;

    public function getUnifiedNameSpaceClassMapProvider(): UnifiedNameSpaceClassMapProvider
    {
        if (!is_a($this->unifiedNameSpaceClassMapProvider, UnifiedNameSpaceClassMapProvider::class)) {
            $this->unifiedNameSpaceClassMapProvider = new UnifiedNameSpaceClassMapProvider();
        }

        return $this->unifiedNameSpaceClassMapProvider;
    }

    public function getBackwardsCompatibilityClassMapProvider(): BackwardsCompatibilityClassMapProvider
    {
        if (!is_a($this->backwardsCompatibilityClassMapProvider, BackwardsCompatibilityClassMapProvider::class)) {
            $this->backwardsCompatibilityClassMapProvider = new BackwardsCompatibilityClassMapProvider();
        }

        return $this->backwardsCompatibilityClassMapProvider;
    }

    public function getModuleExtendClassMapProvider(): ModuleExtendClassMapProvider
    {
        if (!is_a($this->moduleExtendClassMapProvider, ModuleExtendClassMapProvider::class)) {
            $modulesDirectory = Path::join((new ProjectDirectoriesLocator())->getSourcePath(), $this->scanForDirectory);
            $scanner = new DirectoryScanner($this->scanForFilename, $modulesDirectory);
            $parser = new ModuleMetadataParser($scanner);
            $this->moduleExtendClassMapProvider =  new ModuleExtendClassMapProvider($parser);
        }

        return $this->moduleExtendClassMapProvider;
    }

    public function getGenerator(): Generator
    {
        return new Generator(
            $this->getUnifiedNameSpaceClassMapProvider(),
            $this->getBackwardsCompatibilityClassMapProvider(),
            $this->getModuleExtendClassMapProvider()
        );
    }
}
