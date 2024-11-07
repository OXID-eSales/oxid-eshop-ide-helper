<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\Core;

class ModuleMetadataParser
{
    public function __construct(private readonly DirectoryScanner $scanner)
    {
    }

    public function getChainExtendedClasses(): array
    {
        $chainExtendMap = [];
        foreach ($this->scanner->getFilePaths() as $path) {
            $aModule = [];
            include($path);
            if (isset($aModule['extend'])) {
                $chainExtendMap = array_merge($chainExtendMap, array_flip($aModule['extend']));
            }
        }

        return $chainExtendMap;
    }
}
