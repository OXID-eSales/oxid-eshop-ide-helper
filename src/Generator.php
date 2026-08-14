<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

readonly class Generator
{
    public function __construct(
        private ModuleExtendClassMapProvider $moduleExtendClassMapProvider,
    ) {
    }

    public function generate(): void
    {
        $fileSystem = new Filesystem();
        $outputDirectory = (new ProjectRootLocator())->getProjectRoot();
        $fileSystem->dumpFile(
            Path::join($outputDirectory, '.phpstorm.meta.php', 'oxid.meta.php'),
            $this->generatePhpStormIdeHelperOutput()
        );
    }

    private function generatePhpStormIdeHelperOutput(): string
    {
        return $this->getTwig()
            ->render(
                'phpstorm.meta.html.twig',
                ['moduleParentClasses' => $this->moduleExtendClassMapProvider->getModuleParentClassMap()]
            );
    }

    protected function getTwig(): Environment
    {
        return new Environment(new FilesystemLoader(Path::join(__DIR__, 'templates')));
    }
}
