<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper\Core;

/**
 * Class ModuleMetadataParser: parse module metadata.php extend section.
 */
class ModuleMetadataParser
{
    /**
     * @var DirectoryScanner
     */
    private $scanner;

    /**
     * ModuleMetadataParser constructor.
     *
     * @param DirectoryScanner $scanner
     */
    public function __construct(DirectoryScanner $scanner)
    {
        $this->scanner = $scanner;
    }

    /**
     * Get all module chain extensions.
     * Key is module class, value is shop class.
     *
     * @return array
     */
    public function getChainExtendedClasses()
    {
        $chainExtendMap = [];
        $paths = $this->scanner->getFilePaths();

        foreach ($paths as $path) {
            $aModule = [];
            include($path);
            if (isset($aModule['extend'])) {
                $chainExtendMap = array_merge($chainExtendMap, array_flip($aModule['extend']));
            }
        }
        return $chainExtendMap;
    }
}
