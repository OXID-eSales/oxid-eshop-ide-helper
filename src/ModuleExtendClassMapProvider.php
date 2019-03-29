<?php
/**
 * This file is part of OXID eSales IDE code completion helper script.
 *
 * OXID eSales IDE code completion helper script is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales IDE code completion helper script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales IDE code completion helper script. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
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