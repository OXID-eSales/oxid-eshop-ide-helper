<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\tests\Integration;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;
use OxidEsales\EshopIdeHelper\Generator;
use OxidEsales\TestModule\Model\Article_parent;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class GeneratorTest extends TestCase
{
    private readonly string $ideHelperFile;
    private readonly string $ideHelperFileBackup;
    private readonly string $phpstormIdeHelperFile;
    private readonly string $phpstormIdeHelperFileBackup;
    private readonly Filesystem $filesystem;
    private readonly string $fixtures;

    public function setUp(): void
    {
        parent::setUp();

        $projectRoot = (new ProjectRootLocator())->getProjectRoot();
        $this->filesystem = new Filesystem();
        $this->fixtures = Path::join(__DIR__, 'Fixtures');
        $this->ideHelperFile = Path::join(
            $projectRoot,
            '.ide-helper.php'
        );
        $this->phpstormIdeHelperFile = Path::join(
            $projectRoot,
            '.phpstorm.meta.php',
            'oxid.meta.php'
        );
        $this->ideHelperFileBackup = "$this->ideHelperFile.back";
        $this->phpstormIdeHelperFileBackup = "$this->phpstormIdeHelperFile.back";

        $this->backupHelperFiles();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->restoreHelperFiles();
    }

    public function testGenerateWithClassMapsMissmatch(): void
    {
        $testCase = 'ClassMapsMissmatch';
        (new Generator(
            $this->getUnifiedNameSpaceClassMapProvider($testCase),
            $this->getBackwardsCompatibilityClassMapProvider($testCase),
            $this->getModuleExtendClassMapProvider($testCase)
        ))
            ->generate();

        $this->assertFileEquals($this->getExpectedFile($testCase), $this->ideHelperFile);
        $this->assertFileExists($this->phpstormIdeHelperFile);
    }

    public function testGenerateValidCases(): void
    {
        $testCase = 'Valid';
        (new Generator(
            $this->getUnifiedNameSpaceClassMapProvider($testCase),
            $this->getBackwardsCompatibilityClassMapProvider($testCase),
            $this->getModuleExtendClassMapProvider($testCase)
        ))
            ->generate();

        $this->assertFileEquals($this->getExpectedFile($testCase), $this->ideHelperFile);
        $this->assertFileExists($this->phpstormIdeHelperFile);
    }

    public function testGenerateValidCasesWillProduceValidPhpStormHelperFile(): void
    {
        $testCase = 'Valid';
        (new Generator(
            $this->getUnifiedNameSpaceClassMapProvider($testCase),
            $this->getBackwardsCompatibilityClassMapProvider($testCase),
            $this->getModuleExtendClassMapProvider($testCase)
        ))
            ->generate();

        include $this->phpstormIdeHelperFile;

        $this->assertEquals(Article::class, get_parent_class(new Article_parent()));
    }

    private function getUnifiedNameSpaceClassMapProvider(string $testCase): UnifiedNameSpaceClassMapProvider
    {
        return $this->createConfiguredMock(
            UnifiedNameSpaceClassMapProvider::class,
            [
                'getClassMap' => include Path::join(
                    $this->fixtures,
                    $testCase,
                    'UnifiedNameSpaceClassMap.php'
                ),
            ]
        );
    }

    private function getBackwardsCompatibilityClassMapProvider(string $testCase): BackwardsCompatibilityClassMapProvider
    {
        return $this->createConfiguredMock(
            BackwardsCompatibilityClassMapProvider::class,
            [
                'getClassMap' => array_flip(
                    include Path::join(
                        $this->fixtures,
                        $testCase,
                        'BackwardsCompatibilityClassMap.php'
                    )
                ),
            ]
        );
    }

    private function getModuleExtendClassMapProvider(string $testCase): ModuleExtendClassMapProvider
    {
        return $this->createConfiguredMock(
            ModuleExtendClassMapProvider::class,
            [
                'getModuleParentClassMap' =>
                    include Path::join(
                        $this->fixtures,
                        $testCase,
                        'ModuleExtendClassMap.php'
                    ),
            ]
        );
    }

    private function getExpectedFile(string $testCase): string
    {
        return Path::join(
            $this->fixtures,
            $testCase,
            '.ide-helper.php'
        );
    }

    private function backupHelperFiles(): void
    {
        $this->filesystem->copy($this->ideHelperFile, $this->ideHelperFileBackup, true);
        $this->filesystem->copy($this->phpstormIdeHelperFile, $this->phpstormIdeHelperFileBackup, true);
        $this->filesystem->remove($this->ideHelperFile);
        $this->filesystem->remove($this->phpstormIdeHelperFile);
    }

    private function restoreHelperFiles(): void
    {
        $this->filesystem->copy($this->ideHelperFileBackup, $this->ideHelperFile, true);
        $this->filesystem->copy($this->phpstormIdeHelperFileBackup, $this->phpstormIdeHelperFile, true);
        $this->filesystem->remove($this->ideHelperFileBackup);
        $this->filesystem->remove($this->phpstormIdeHelperFileBackup);
    }
}
