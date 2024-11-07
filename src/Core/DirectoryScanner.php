<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\Core;

use Symfony\Component\Filesystem\Path;

class DirectoryScanner
{
    private array $filePaths = [];
    private string $searchForFileName;

    public function __construct(string $searchForFileName, private readonly string $startPath)
    {
        $this->searchForFileName = strtolower($searchForFileName);
    }

    public function getFilePaths(): array
    {
        $this->scanDirectory($this->startPath);

        return $this->filePaths;
    }


    private function scanDirectory($directoryPath): void
    {
        if (is_dir($directoryPath)) {
            $files = scandir($directoryPath);
            foreach ($files as $fileName) {
                $filePath = Path::join($directoryPath, $fileName);
                if (is_dir($filePath) && !\in_array($fileName, ['.', '..'])) {
                    $this->scanDirectory($filePath);
                } elseif ($this->searchForFileName === strtolower($fileName)) {
                    $this->filePaths[] = $filePath;
                }
            }
        }
    }
}
