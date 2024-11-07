<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

readonly class Generator
{
    public function __construct(
        private UnifiedNameSpaceClassMapProvider $unifiedNameSpaceClassMapProvider,
        private BackwardsCompatibilityClassMapProvider $backwardsCompatibilityClassMapProvider,
        private ModuleExtendClassMapProvider $moduleExtendClassMapProvider,
    ) {
    }

    public function generate(): void
    {
        $fileSystem = new Filesystem();
        $outputDirectory = (new ProjectRootLocator())->getProjectRoot();
        $fileSystem->dumpFile(
            Path::join($outputDirectory, '.ide-helper.php'),
            $this->generateIdeHelperOutput()
        );
        $fileSystem->dumpFile(
            Path::join($outputDirectory, '.phpstorm.meta.php', 'oxid.meta.php'),
            $this->generatePhpStormIdeHelperOutput()
        );
    }

    private function generateIdeHelperOutput(): string
    {
        $backwardsCompatibleClasses = [];
        $backwardsCompatibilityMap = $this->getBackwardsCompatibilityMap();

        foreach ($backwardsCompatibilityMap as $fullyQualifiedUnifiedNamespaceClass => $backwardsCompatibleClass) {
            $backwardsCompatibleClassMetaInformation = $this->collectInheritanceInformation(
                $backwardsCompatibleClass,
                $fullyQualifiedUnifiedNamespaceClass
            );
            if (!empty($backwardsCompatibleClassMetaInformation)) {
                $backwardsCompatibleClasses[] = $backwardsCompatibleClassMetaInformation;
            }
        }

        $output = $this->getTwig()
            ->render(
                'main-template.html.twig',
                ['backwardsCompatibleClasses' => $backwardsCompatibleClasses]
            );
        if (empty($output)) {
            throw new \RuntimeException('Generation of the ide-helper content failed.');
        }

        return $output;
    }

    private function generatePhpStormIdeHelperOutput(): string
    {
        return $this->getTwig()
            ->render(
                'phpstorm.meta.html.twig',
                ['moduleParentClasses' => $this->moduleExtendClassMapProvider->getModuleParentClassMap()]
            );
    }

    private function collectInheritanceInformation(
        $backwardsCompatibleClassName,
        $fullyQualifiedUnifiedNamespaceClassName
    ): array {
        $backwardsCompatibleClassMetaInformation = [];
        $unifiedNamespaceClassMap = $this->getUnifiedNamespaceClassMap();

        if (\array_key_exists($fullyQualifiedUnifiedNamespaceClassName, $unifiedNamespaceClassMap)) {
            $backwardsCompatibleClassMetaInformation = [
                'isAbstract' => $unifiedNamespaceClassMap[$fullyQualifiedUnifiedNamespaceClassName]['isAbstract'],
                'isInterface' => $unifiedNamespaceClassMap[$fullyQualifiedUnifiedNamespaceClassName]['isInterface'],
                'childClassName' => $backwardsCompatibleClassName,
                'parentClassName' => $fullyQualifiedUnifiedNamespaceClassName
            ];
        }
        return $backwardsCompatibleClassMetaInformation;
    }

    private function getBackwardsCompatibilityMap(): array
    {
        return $this->backwardsCompatibilityClassMapProvider->getClassMap();
    }

    private function getUnifiedNamespaceClassMap(): array
    {
        return $this->unifiedNameSpaceClassMapProvider->getClassMap();
    }

    protected function getTwig(): Environment
    {
        return new Environment(new FilesystemLoader(Path::join(__DIR__, 'templates')));
    }
}
