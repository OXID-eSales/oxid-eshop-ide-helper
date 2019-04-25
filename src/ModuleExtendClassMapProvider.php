<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopIdeHelper;

use \OxidEsales\Facts\Facts;

/**
 * Class ModuleExtendClassMapProvider
 *
 * @package OxidEsales\EshopIdeHelper
 */
class ModuleExtendClassMapProvider
{
    const SCAN_FOR_FILENAME = 'metadata.php';

    /** @var Facts */
    private $facts = null;

    /**
     * @var array
     */
    private $metadataFilePaths = [];

    /**
     * Generator constructor.
     *
     * @param Facts $facts
     */
    public function __construct(Facts $facts) {
        $this->facts = $facts;
    }

    /**
     * @return array
     */
    public function getModuleParentClassMap()
    {
        $map = [];
        $extends = $this->getChainExtendedClasses();
        foreach ($extends as $key => $value) {
            $tmp = explode("\\", $key);
            $map[] = [
                'isAbstract'      => false,
                'isInterface'     => false,
                'childClassName'  => array_pop($tmp) . '_parent',
                'parentClassName' => $value,
                'namespace'       => implode("\\", $tmp)
            ];
        }

        return $map;
    }

    /**
     * Get all module chain extensions.
     *
     * @return array
     */
    private function getChainExtendedClasses()
    {
        $chainExtendMap = [];
        $paths = $this->getModuleMetadataFilePaths();

        foreach ($paths as $path) {
            $aModule = [];
            include($path);
            if (isset($aModule['extend'])) {
                $chainExtendMap = array_merge($chainExtendMap, array_flip($aModule['extend']));
            }
        }
        return $chainExtendMap;
    }

    /**
     * Scan shop modules directory.
     *
     * @return array
     */
    private function getModuleMetadataFilePaths()
    {
        if (empty($this->metadataFilePaths)) {
            $modulesDirectory = $this->facts->getSourcePath() . DIRECTORY_SEPARATOR . 'modules';
            $this->scanDirectory($modulesDirectory);
        }

        return $this->metadataFilePaths;
    }

    /**
     * Clean temp folder content.
     *
     * @param string $clearFolderPath Sub-folder path to check for search file name.
     */
    private function scanDirectory($directoryPath)
    {
        if (is_dir($directoryPath)) {
            $files = scandir($directoryPath);
            foreach ($files as $fileName) {
                $filePath = $directoryPath . DIRECTORY_SEPARATOR . $fileName;
                if (is_dir($filePath) && !in_array($fileName, ['.', '..'])) {
                    $this->scanDirectory($filePath);
                } elseif (self::SCAN_FOR_FILENAME == strtolower($fileName)) {
                    $this->metadataFilePaths[] = $filePath;
                }
            }
        }
    }
}