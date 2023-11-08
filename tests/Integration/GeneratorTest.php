<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\tests\Integration;

use OxidEsales\EshopIdeHelper\Generator;
use OxidEsales\Facts\Facts;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;
use OxidEsales\UnifiedNameSpaceGenerator\Exceptions\OutputDirectoryValidationException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;

final class GeneratorTest extends TestCase
{
    private null|vfsStreamDirectory $vfsStreamDirectory = null;

    private static string $rootDir = 'root';

    public static function providerClassMaps(): array
    {
        return [
            /**
             * In the BackwardscompatiblityClassMap.php, there are listed also Enterprise classes. In case we
             * want to generate the ide-helper for the CE edition, those classes are not found in the
             * UnifiedNamespaceClassMap.php
             */
            ['NotMatchingClassMapsLikeInEnterpriseEdition'],

            /**
             * Matching class maps, testing abstract, interface and class
             */
            ['Valid']
        ];
    }

    /**
     * @dataProvider providerClassMaps
     */
    public function testGenerateValidCases(string $testCaseFolder): void
    {
        $pathToUnifiedNameSpaceClassMap = Path::join(
            $this->getPathToTestData(),
            $testCaseFolder,
            "UnifiedNameSpaceClassMap.php"
        );
        $pathToBackwardsCompatibilityClassMap = Path::join(
            $this->getPathToTestData(),
            $testCaseFolder,
            "BackwardsCompatibilityClassMap.php"
        );
        $pathToIdeHelperOutput = Path::join($this->getPathToTestData(), $testCaseFolder, ".ide-helper.php");
        $pathToModuleExtendClassMap = Path::join($this->getPathToTestData(), 'Valid', "ModuleExtendClassMap.php");

        $generator = new Generator(
            $this->getFactsMock(0777),
            $this->getUnifiedNameSpaceClassMapProviderMock($pathToUnifiedNameSpaceClassMap),
            $this->getBackwardsCompatibilityClassMapProviderMock($pathToBackwardsCompatibilityClassMap),
            $this->getModuleExtendClassMapProviderMock($pathToModuleExtendClassMap)
        );
        $generator->generate();

        $this->assertFileEquals(
            $pathToIdeHelperOutput,
            Path::join(
                $this->getVirtualOutputDirectory(),
                '.ide-helper.php'
            )
        );
    }

    public function testGenerateOutputFileCanNotBeWritten(): void
    {
        $pathToUnifiedNameSpaceClassMap = Path::join(
            $this->getPathToTestData(),
            'Valid',
            "UnifiedNameSpaceClassMap.php"
        );
        $pathToBackwardsCompatibilityClassMap = Path::join(
            $this->getPathToTestData(),
            'Valid',
            "BackwardsCompatibilityClassMap.php"
        );
        $pathToModuleExtendClassMap = Path::join($this->getPathToTestData(), 'Valid', "ModuleExtendClassMap.php");

        $generator = new Generator(
            $this->getFactsMock(0555),
            $this->getUnifiedNameSpaceClassMapProviderMock($pathToUnifiedNameSpaceClassMap),
            $this->getBackwardsCompatibilityClassMapProviderMock($pathToBackwardsCompatibilityClassMap),
            $this->getModuleExtendClassMapProviderMock($pathToModuleExtendClassMap, 'never')
        );
        $this->expectException(OutputDirectoryValidationException::class);
        $generator->generate();
    }

    public function testGeneratePhpStormIdeHelper(): void
    {
        $pathToUnifiedNameSpaceClassMap = Path::join(
            $this->getPathToTestData(),
            'Valid',
            "UnifiedNameSpaceClassMap.php"
        );
        $pathToBackwardsCompatibilityClassMap = Path::join(
            $this->getPathToTestData(),
            'Valid',
            "BackwardsCompatibilityClassMap.php"
        );
        $pathToModuleExtendClassMap = Path::join($this->getPathToTestData(), 'Valid', "ModuleExtendClassMap.php");

        $generator = new Generator(
            $this->getFactsMock(0777),
            $this->getUnifiedNameSpaceClassMapProviderMock($pathToUnifiedNameSpaceClassMap),
            $this->getBackwardsCompatibilityClassMapProviderMock($pathToBackwardsCompatibilityClassMap),
            $this->getModuleExtendClassMapProviderMock($pathToModuleExtendClassMap)
        );
        $generator->generate();

        $this->assertFileExists(Path::join($this->getVirtualOutputDirectory(), '.phpstorm.meta.php/oxid.meta.php'));
    }

    /**
     * @return MockObject|Facts
     */
    private function getFactsMock($permissionsForShopRootPath)
    {
        $factsMock = $this->getMockBuilder(Facts::class)
            ->onlyMethods(['getShopRootPath'])
            ->getMock();
        $factsMock->expects($this->any())
            ->method('getShopRootPath')
            ->willReturn($this->getVirtualOutputDirectory($permissionsForShopRootPath));
        return $factsMock;
    }


    private function getUnifiedNameSpaceClassMapProviderMock(string $pathToUnifiedNameSpaceClassMap): MockObject
    {
        $unifiedNamespaceClassMap = include $pathToUnifiedNameSpaceClassMap;

        $unifiedNameSpaceClassMapProviderMock = $this->getMockBuilder(UnifiedNameSpaceClassMapProvider::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getClassMap'])
            ->getMock();
        $unifiedNameSpaceClassMapProviderMock->expects($this->any())
            ->method('getClassMap')
            ->willReturn($unifiedNamespaceClassMap);

        return $unifiedNameSpaceClassMapProviderMock;
    }

    /**
     * @param string $pathToBackwardsCompatibilityClassMap
     *
     * @return MockObject|BackwardsCompatibilityClassMapProvider
     */
    private function getBackwardsCompatibilityClassMapProviderMock(string $pathToBackwardsCompatibilityClassMap)
    {
        $backwardsCompatibilityClassMap = include $pathToBackwardsCompatibilityClassMap;

        $backwardsCompatibilityClassMapProviderMock = $this->getMockBuilder(
            BackwardsCompatibilityClassMapProvider::class
        )
            ->disableOriginalConstructor()
            ->onlyMethods(['getClassMap'])
            ->getMock();
        $backwardsCompatibilityClassMapProviderMock->expects($this->once())
            ->method('getClassMap')
            ->willReturn(array_flip($backwardsCompatibilityClassMap));

        return $backwardsCompatibilityClassMapProviderMock;
    }

    /**
     * @param string $pathToModuleExtendClassMap
     * @param string $expectationMethod
     *
     * @return MockObject|ModuleExtendClassMapProvider
     */
    private function getModuleExtendClassMapProviderMock($pathToModuleExtendClassMap, $expectationMethod = 'once')
    {
        $moduleExtendClassMap = include $pathToModuleExtendClassMap;

        $moduleExtendClassMapProviderMock = $this->getMockBuilder(ModuleExtendClassMapProvider::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getModuleParentClassMap'])
            ->getMock();
        $moduleExtendClassMapProviderMock->expects($this->$expectationMethod())
            ->method('getModuleParentClassMap')
            ->willReturn($moduleExtendClassMap);

        return $moduleExtendClassMapProviderMock;
    }

    private function getPathToTestData()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'testData' . DIRECTORY_SEPARATOR;
    }

    private function getVirtualOutputDirectory(int $permissions = 0777, array $structure = null): string
    {
        if (!is_array($structure)) {
            $structure = [];
        }

        vfsStream::create($structure, $this->getVfsStreamDirectory());
        $directory = $this->getVfsRootPath();
        chmod($directory, $permissions);

        return $directory;
    }

    private function getVfsStreamDirectory(): vfsStreamDirectory
    {
        if (is_null($this->vfsStreamDirectory)) {
            $this->vfsStreamDirectory = vfsStream::setup(self::$rootDir);
        }

        return $this->vfsStreamDirectory;
    }

    private function getVfsRootPath(): string
    {
        return vfsStream::url(self::$rootDir) . DIRECTORY_SEPARATOR;
    }
}
