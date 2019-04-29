<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper;

use \Webmozart\PathUtil\Path;
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
    const SCAN_FOR_FILENAME = 'metadata.php';

    const SCAN_FOR_DIRECTORY = 'modules';

    /** @var Facts */
    private $facts;

    /**
     * @var UnifiedNameSpaceClassMapProvider
     */
    private $unifiedNameSpaceClassMapProvider;

    /**
     * @var BackwardsCompatibilityClassMapProvider
     */
    private $backwardsCompatibilityClassMapProvider;

    /**
     * @var ModuleExtendClassMapProvider
     */
    private $moduleExtendClassMapProvider;

    /**
     * @return Facts
     */
    public function getFacts()
    {
        if (!is_a($this->facts, Facts::class)) {
            $this->facts = new Facts();
        }

        return $this->facts;
    }

    /**
     * @return UnifiedNameSpaceClassMapProvider
     */
    public function getUnifiedNameSpaceClassMapProvider()
    {
        if (!is_a($this->unifiedNameSpaceClassMapProvider, UnifiedNameSpaceClassMapProvider::class)) {
            $this->unifiedNameSpaceClassMapProvider = new UnifiedNameSpaceClassMapProvider($this->getFacts());
        }

        return $this->unifiedNameSpaceClassMapProvider;
    }

    /**
     * @return BackwardsCompatibilityClassMapProvider
     */
    public function getBackwardsCompatibilityClassMapProvider()
    {
        if (!is_a($this->backwardsCompatibilityClassMapProvider, BackwardsCompatibilityClassMapProvider::class)) {
            $this->backwardsCompatibilityClassMapProvider = new BackwardsCompatibilityClassMapProvider($this->getFacts());
        }

        return $this->backwardsCompatibilityClassMapProvider;
    }

    /**
     * @return ModuleExtendClassMapProvider
     */
    public function getModuleExtendClassMapProvider()
    {
        if (!is_a($this->moduleExtendClassMapProvider, ModuleExtendClassMapProvider::class)) {
            $modulesDirectory = Path::join($this->facts->getSourcePath(), self::SCAN_FOR_DIRECTORY);
            $scanner = new DirectoryScanner(self::SCAN_FOR_FILENAME, $modulesDirectory);
            $parser = new ModuleMetadataParser($scanner);
            $this->moduleExtendClassMapProvider =  new ModuleExtendClassMapProvider($parser);
        }

        return $this->moduleExtendClassMapProvider;
    }

    /**
     * @return Generator
     */
    public function getGenerator()
    {
        $generator = new Generator(
            $this->getFacts(),
            $this->getUnifiedNameSpaceClassMapProvider(),
            $this->getBackwardsCompatibilityClassMapProvider(),
            $this->getModuleExtendClassMapProvider()
        );
        return $generator;
    }
}
