<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper\Core;

use \Webmozart\PathUtil\Path;

/**
 * Class DirectoryScanner: Recursively scan given path for matching files (case insensitive).
 */
class DirectoryScanner
{
    /**
     * @var array
     */
    private $filePaths = [];

    /**
     * @var string
     */
    private $searchForFileName = '';

    /**
     * @var string
     */
    private $startPath = '';

    /**
     * DirectoryScanner constructor.
     *
     * @param string $searchForFileName
     * @param string $startPath
     */
    public function __construct($searchForFileName, $startPath)
    {
        $this->searchForFileName = strtolower($searchForFileName);
        $this->startPath = $startPath;
    }

    /**
     * Scan shop modules directory.
     *
     * @return array
     */
    public function getFilePaths()
    {
        $this->scanDirectory($this->startPath);

        return $this->filePaths;
    }

    /**
     * Recursive search for matching files.
     *
     * @param string $clearFolderPath Sub-folder path to check for search file name.
     */
    private function scanDirectory($directoryPath)
    {
        if (is_dir($directoryPath)) {
            $files = scandir($directoryPath);
            foreach ($files as $fileName) {
                $filePath = Path::join($directoryPath, $fileName);
                if (is_dir($filePath) && !in_array($fileName, ['.', '..'])) {
                    $this->scanDirectory($filePath);
                } elseif ($this->searchForFileName == strtolower($fileName)) {
                    $this->filePaths[] = $filePath;
                }
            }
        }
    }
}
