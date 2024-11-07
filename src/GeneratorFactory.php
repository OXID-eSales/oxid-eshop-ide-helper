<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectDirectoriesLocator;
use Symfony\Component\Filesystem\Path;
use OxidEsales\EshopIdeHelper\Core\DirectoryScanner;
use OxidEsales\EshopIdeHelper\Core\ModuleMetadataParser;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;

readonly class GeneratorFactory
{
    public function create(): Generator
    {
        return new Generator(
            new UnifiedNameSpaceClassMapProvider(),
            new BackwardsCompatibilityClassMapProvider(),
            new ModuleExtendClassMapProvider(
                new ModuleMetadataParser(
                    new DirectoryScanner(
                        'metadata.php',
                        Path::join(
                            (new ProjectDirectoriesLocator())->getSourcePath(),
                            'modules'
                        )
                    )
                )
            )
        );
    }
}
