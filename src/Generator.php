<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper;

use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\Exceptions\OutputDirectoryValidationException;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Generator
{
    public function __construct(
        private readonly Facts $facts,
        private readonly UnifiedNameSpaceClassMapProvider $unifiedNameSpaceClassMapProvider,
        private readonly BackwardsCompatibilityClassMapProvider $backwardsCompatibilityClassMapProvider,
        private readonly ModuleExtendClassMapProvider $moduleExtendClassMapProvider,
        private readonly string $templateDir = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
        private readonly Filesystem $fileSystem = new Filesystem(),
        private readonly int $fileWriteCodeError = 1
    ) {
    }

    public function generate(): void
    {
        $output = $this->generateIdeHelperOutput();
        $this->writeIdeHelperFile($output, '.ide-helper.php');

        $outputForPhpStormIde = $this->generatePhpStormIdeHelperOutput();
        $this->writeIdeHelperFile($outputForPhpStormIde, '.phpstorm.meta.php/oxid.meta.php');
    }

    protected function generateIdeHelperOutput(): string
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

        $twig = $this->getTwig();
        $output = $twig->render(
            'main-template.html.twig',
            ['backwardsCompatibleClasses' => $backwardsCompatibleClasses]
        );
        if (!is_string($output) || empty($output)) {
            throw new OutputDirectoryValidationException('Generation of the ide-helper content failed.');
        }

        return $output;
    }

    protected function generatePhpStormIdeHelperOutput(): string
    {
        $twig = $this->getTwig();
        return $twig->render(
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

        if (array_key_exists($fullyQualifiedUnifiedNamespaceClassName, $unifiedNamespaceClassMap)) {
            $backwardsCompatibleClassMetaInformation = [
                'isAbstract'      => $unifiedNamespaceClassMap[$fullyQualifiedUnifiedNamespaceClassName]['isAbstract'],
                'isInterface'     => $unifiedNamespaceClassMap[$fullyQualifiedUnifiedNamespaceClassName]['isInterface'],
                'childClassName'  => $backwardsCompatibleClassName,
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
        $loader = new FilesystemLoader($this->templateDir);

        return new Environment($loader);
    }

    protected function validateOutputDirectoryPermissions($outputDirectory): void
    {
        if (!is_dir($outputDirectory)) {
            throw new OutputDirectoryValidationException(
                'The directory "' . $outputDirectory . '" where the ide-helper file has to be written to' .
                ' does not exist. ' .
                'Please create the directory "' . $outputDirectory . '" with write permissions for the user "' .
                get_current_user() . '" ' . 'and run this script again',
                $this->fileWriteCodeError
            );
        } elseif (!is_writable($outputDirectory)) {
            throw new OutputDirectoryValidationException(
                'The directory "' . realpath($outputDirectory) . '" where the class files have to be written to' .
                ' is not writable for user "' . get_current_user() . '". ' .
                'Please fix the permissions on this directory ' .
                'and run this script again',
                $this->fileWriteCodeError
            );
        }
    }

    private function writeIdeHelperFile($output, $fileName): void
    {
        $outputDirectory = $this->facts->getShopRootPath();
        $this->validateOutputDirectoryPermissions($outputDirectory);

        $this->fileSystem->dumpFile(Path::join($outputDirectory, $fileName), $output);
    }
}
