<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\Core;

class ModuleExtendClassMapProvider
{
    public function __construct(private readonly ModuleMetadataParser $parser)
    {
    }

    public function getModuleParentClassMap(): array
    {
        $map = [];
        foreach ($this->parser->getChainExtendedClasses() as $key => $value) {
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
