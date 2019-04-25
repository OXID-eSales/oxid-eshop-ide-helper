<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopIdeHelper;

use \OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use \OxidEsales\UnifiedNameSpaceGenerator\BackwardsCompatibilityClassMapProvider;
use \OxidEsales\UnifiedNameSpaceGenerator\Exceptions\OutputDirectoryValidationException;
use \OxidEsales\Facts\Facts;
use \Symfony\Component\Filesystem\Filesystem;
use \Webmozart\PathUtil\Path;
use OxidEsales\EshopIdeHelper\Core\ModuleExtendClassMapProvider;

/**
 * Class Generator
 *
 * @package OxidEsales\EshopIdeHelper
 */
class Generator
{
    /** @var Facts */
    private $facts = null;

    /** @var BackwardsCompatibilityClassMapProvider */
    private $backwardsCompatibilityClassMapProvider = null;

    /** @var UnifiedNameSpaceClassMapProvider */
    private $unifiedNameSpaceClassMapProvider = null;

    /**
     * @var ModuleExtendClassMapProvider
     */
    private $moduleExtendClassMapProvider = null;

    /** @var Filesystem */
    private $fileSystem = null;

    const ERROR_CODE_FILE_WRITE_ERROR = 1;

    /**
     * Generator constructor.
     *
     * @param Facts                                  $facts
     * @param UnifiedNameSpaceClassMapProvider       $unifiedNameSpaceClassMapProvider
     * @param BackwardsCompatibilityClassMapProvider $backwardsCompatibilityClassMapProvider
     * @param ModuleExtendClassMapProvider           $moduleExtendClassMapProvider
     */
    public function __construct(
        Facts $facts,
        UnifiedNameSpaceClassMapProvider $unifiedNameSpaceClassMapProvider,
        BackwardsCompatibilityClassMapProvider $backwardsCompatibilityClassMapProvider,
        ModuleExtendClassMapProvider $moduleExtendClassMapProvider
    ) {
        $this->facts = $facts;
        $this->unifiedNameSpaceClassMapProvider = $unifiedNameSpaceClassMapProvider;
        $this->backwardsCompatibilityClassMapProvider = $backwardsCompatibilityClassMapProvider;
        $this->moduleExtendClassMapProvider = $moduleExtendClassMapProvider;

        $this->fileSystem = new Filesystem();
    }

    /**
     * Generate a helper file for IDE auto-completion
     */
    public function generate()
    {
        $output = $this->generateIdeHelperOutput();
        $this->writeIdeHelperFile($output, '.ide-helper.php');

        $outputForPhpStormIde = $this->generatePhpStormIdeHelperOutput();
        $this->writeIdeHelperFile($outputForPhpStormIde, '.phpstorm.meta.php/oxid.meta.php');
    }

    /**
     * Generate the helper classes for a given class map
     *
     * @return mixed|string
     *
     * @throws \Exception
     */
    protected function generateIdeHelperOutput()
    {
        $backwardsCompatibleClasses = [];
        $backwardsCompatibilityMap = $this->getBackwardsCompatibilityMap();

        foreach ($backwardsCompatibilityMap as $fullyQualifiedUnifiedNamespaceClassName => $backwardsCompatibleClassName) {
            $backwardsCompatibleClassMetaInformation = $this->collectInheritanceInformation(
                $backwardsCompatibleClassName,
                $fullyQualifiedUnifiedNamespaceClassName
            );
            if (!empty($backwardsCompatibleClassMetaInformation)) {
                $backwardsCompatibleClasses[] = $backwardsCompatibleClassMetaInformation;
            }
        }

        $smarty = $this->getSmarty();
        $smarty->assign('backwardsCompatibleClasses', $backwardsCompatibleClasses);
        $output = $smarty->fetch('main-template.tpl');
        if (!is_string($output) || empty($output)) {
            throw new OutputDirectoryValidationException('Generation of the ide-helper content failed.');
        }

        return $output;
    }
    
    /**
     * Generate the helper classes for a given class map
     *
     * @return mixed|string
     */
    protected function generatePhpStormIdeHelperOutput()
    {
        $smarty = $this->getSmarty();
        $smarty->assign('moduleParentClasses', $this->moduleExtendClassMapProvider->getModuleParentClassMap());
        $output = $smarty->fetch('phpstorm.meta.php.tpl');

        return $output;
    }

    /**
     * @param string $backwardsCompatibleClassName
     * @param string $fullyQualifiedUnifiedNamespaceClassName
     *
     * @return array The backwards compatible classes with meta-information if it should be e.g. an abstract class
     *
     * @throws \Exception when the inheritance
     */
    private function collectInheritanceInformation($backwardsCompatibleClassName, $fullyQualifiedUnifiedNamespaceClassName)
    {
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


    /**
     * @return array
     */
    private function getBackwardsCompatibilityMap()
    {
        return $this->backwardsCompatibilityClassMapProvider->getClassMap();
    }

    /**
     * @return array
     */
    private function getUnifiedNamespaceClassMap()
    {
        return $this->unifiedNameSpaceClassMapProvider->getClassMap();
    }


    /**
     * Return an instance of smarty
     *
     * @return \Smarty
     */
    protected function getSmarty()
    {
        $smarty = new \Smarty();
        $currentDirectory = dirname(__FILE__);
        $smarty->template_dir = realpath(
            $currentDirectory . DIRECTORY_SEPARATOR .
            'smarty' . DIRECTORY_SEPARATOR .
            'templates' . DIRECTORY_SEPARATOR
        );
        $smarty->compile_dir = realpath(
            $currentDirectory . '' . DIRECTORY_SEPARATOR .
            'smarty' . DIRECTORY_SEPARATOR .
            'templates_c' . DIRECTORY_SEPARATOR
        );
        $smarty->left_delimiter = '{{';
        $smarty->right_delimiter = '}}';

        return $smarty;
    }

    /**
     * Validate the permission on the output directory
     *
     * @param string $outputDirectory
     *
     * @throws \Exception
     */
    protected function validateOutputDirectoryPermissions($outputDirectory)
    {
        if (!is_dir($outputDirectory)) {
            throw new OutputDirectoryValidationException(
                'The directory "' . $outputDirectory . '" where the ide-helper file has to be written to' .
                ' does not exist. ' .
                'Please create the directory "' . $outputDirectory . '" with write permissions for the user "' . get_current_user() . '" ' .
                'and run this script again',
                static::ERROR_CODE_FILE_WRITE_ERROR
            );
        } elseif (!is_writable($outputDirectory)) {
            throw new OutputDirectoryValidationException(
                'The directory "' . realpath($outputDirectory) . '" where the class files have to be written to' .
                ' is not writable for user "' . get_current_user() . '". ' .
                'Please fix the permissions on this directory ' .
                'and run this script again',
                static::ERROR_CODE_FILE_WRITE_ERROR
            );
        }
    }

    /**
     * @param string $output
     *
     * @param $fileName
     * @throws \Exception
     */
    private function writeIdeHelperFile($output, $fileName)
    {
        $outputDirectory = $this->facts->getShopRootPath();
        $this->validateOutputDirectoryPermissions($outputDirectory);

        $this->fileSystem->dumpFile(Path::join($outputDirectory, $fileName), $output);
    }
}
