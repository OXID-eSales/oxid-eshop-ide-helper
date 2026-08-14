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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class GeneratorTest extends TestCase
{
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
        $this->phpstormIdeHelperFile = Path::join(
            $projectRoot,
            '.phpstorm.meta.php',
            'oxid.meta.php'
        );
        $this->phpstormIdeHelperFileBackup = "$this->phpstormIdeHelperFile.back";

        $this->backupHelperFiles();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->restoreHelperFiles();
    }

    public function testGenerateValidCasesWillProduceValidPhpStormHelperFile(): void
    {
        $testCase = 'Valid';
        (new Generator(
            $this->getModuleExtendClassMapProvider($testCase)
        ))
            ->generate();

        $this->assertFileExists($this->phpstormIdeHelperFile);

        include $this->phpstormIdeHelperFile;

        $this->assertEquals(Article::class, get_parent_class(new Article_parent()));
    }

    private function getModuleExtendClassMapProvider(string $testCase): ModuleExtendClassMapProvider
    {
        return $this->createConfiguredStub(
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

    private function backupHelperFiles(): void
    {
        $this->filesystem->copy($this->phpstormIdeHelperFile, $this->phpstormIdeHelperFileBackup, true);
        $this->filesystem->remove($this->phpstormIdeHelperFile);
    }

    private function restoreHelperFiles(): void
    {
        $this->filesystem->copy($this->phpstormIdeHelperFileBackup, $this->phpstormIdeHelperFile, true);
        $this->filesystem->remove($this->phpstormIdeHelperFileBackup);
    }
}
