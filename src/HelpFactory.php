<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper;

use Symfony\Component\Filesystem\Path;
use OxidEsales\Facts\Facts;
use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;

/**
 * Class HelpFactory: assemble all needed objects
 */
class HelpFactory
{
    private string $scanForFilename = 'metadata.php';
    private string $scanForDirectory = 'modules';
    private ?Facts $facts = null;
    private ?UnifiedNameSpaceClassMapProvider $unifiedNameSpaceClassMapProvider = null;
    private ?BackwardsCompatibilityClassMapProvider $backwardsCompatibilityClassMapProvider = null;
    private ?ModuleExtendClassMapProvider $moduleExtendClassMapProvider = null;

    public function getFacts(): Facts
    {
        if (!is_a($this->facts, Facts::class)) {
            $this->facts = new Facts();
        }

        return $this->facts;
    }

    public function getUnifiedNameSpaceClassMapProvider(): UnifiedNameSpaceClassMapProvider
    {
        if (!is_a($this->unifiedNameSpaceClassMapProvider, UnifiedNameSpaceClassMapProvider::class)) {
            $this->unifiedNameSpaceClassMapProvider = new UnifiedNameSpaceClassMapProvider($this->getFacts());
        }

        return $this->unifiedNameSpaceClassMapProvider;
    }

    public function getBackwardsCompatibilityClassMapProvider(): BackwardsCompatibilityClassMapProvider
    {
        if (!is_a($this->backwardsCompatibilityClassMapProvider, BackwardsCompatibilityClassMapProvider::class)) {
            $this->backwardsCompatibilityClassMapProvider = new BackwardsCompatibilityClassMapProvider(
                $this->getFacts()
            );
        }

        return $this->backwardsCompatibilityClassMapProvider;
    }

    public function getModuleExtendClassMapProvider(): ModuleExtendClassMapProvider
    {
        if (!is_a($this->moduleExtendClassMapProvider, ModuleExtendClassMapProvider::class)) {
            $modulesDirectory = Path::join($this->facts->getSourcePath(), $this->scanForDirectory);
            $scanner = new DirectoryScanner($this->scanForFilename, $modulesDirectory);
            $parser = new ModuleMetadataParser($scanner);
            $this->moduleExtendClassMapProvider =  new ModuleExtendClassMapProvider($parser);
        }

        return $this->moduleExtendClassMapProvider;
    }

    public function getGenerator(): Generator
    {
        return new Generator(
            $this->getFacts(),
            $this->getUnifiedNameSpaceClassMapProvider(),
            $this->getBackwardsCompatibilityClassMapProvider(),
            $this->getModuleExtendClassMapProvider()
        );
    }
}
