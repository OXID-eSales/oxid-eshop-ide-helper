<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper\Core;

/**
 * Class ModuleExtendClassMap: maps virtual module parent classes to releated shop class.
 */
class ModuleExtendClassMapProvider
{
    /**
     * @var ModuleMetadataParser
     */
    private $parser;

    /**
     * ModuleExtendClassMap constructor.
     *
     * @param ModuleMetadataParser $parser
     */
    public function __construct(ModuleMetadataParser $parser)
    {
       $this->parser = $parser;
    }

    /**
     * @return array
     */
    public function getModuleParentClassMap()
    {
        $map = [];
        $extends = $this->parser->getChainExtendedClasses();
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
}
