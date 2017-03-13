<?php
/**
 * This file is part of OXID eSales IDE code completion helper script.
 *
 * OXID eSales IDE code completion helper script is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales IDE code completion helper script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales IDE code completion helper script. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 */

namespace OxidEsales\EshopIdeHelper;

use OxidEsales\Eshop\Core\Edition\EditionSelector;
use ReflectionClass;
use ReflectionException;

/**
 * Class Generator
 *
 * @package OxidEsales\EshopIdeHelper
 */
class Generator
{

    /**
     * There are some expected missing classes in OXID eSales Community Edition.
     *
     * @var array
     */
    private $bcExpectedMissingClasses = ['oxelement2shoprelationsservice',
                                         'admin_beroles',
                                         'admin_feroles',
                                         'adminlinks_mall',
                                         'admin_mall',
                                         'article_mall',
                                         'article_rights',
                                         'article_rights_buyable_ajax',
                                         'article_rights_visible_ajax',
                                         'attribute_mall',
                                         'category_mall',
                                         'category_rights',
                                         'category_rights_buyable_ajax',
                                         'category_rights_visible_ajax',
                                         'delivery_mall',
                                         'deliveryset_mall',
                                         'discount_mall',
                                         'manufacturer_mall',
                                         'news_mall',
                                         'roles_begroups_ajax',
                                         'roles_belist',
                                         'roles_bemain',
                                         'roles_beobject',
                                         'roles_beuser',
                                         'roles_beuser_ajax',
                                         'roles_fegroups_ajax',
                                         'roles_felist',
                                         'roles_femain',
                                         'roles_feuser',
                                         'selectlist_mall',
                                         'shop_cache',
                                         'shop_mall',
                                         'vendor_mall',
                                         'voucherserie_mall',
                                         'wrapping_mall',
                                         'mallstart',
                                         'oxicachebackend',
                                         'oxfield2shop',
                                         'oxrights',
                                         'oxrole',
                                         'oxshoprelations',
                                         'oxadminrights',
                                         'oxarticle2shoprelations',
                                         'oxcachebackenddefault',
                                         'oxfield2shop',
                                         'oxrights',
                                         'oxrole',
                                         'oxshoprelations',
                                         'oxadminrights',
                                         'oxarticle2shoprelations',
                                         'oxcachebackenddefault',
                                         'oxcachebackendzsdisk',
                                         'oxcachebackendzsshm',
                                         'oxcache',
                                         'oxcachebackend',
                                         'oxcacheitem',
                                         'oxfilecacheconnector',
                                         'oxmemcachedcacheconnector',
                                         'oxreverseproxyconnector',
                                         'oxzenddiskcacheconnector',
                                         'oxzendshmcacheconnector',
                                         'oxreverseproxybackend',
                                         'oxreverseproxyheader',
                                         'oxreverseproxyurlgenerator',
                                         'oxreverseproxyurlpartstoflush',
                                         'oxcategory2shoprelations',
                                         'oxelement2shoprelations',
                                         'oxelement2shoprelationsdbgateway',
                                         'oxelement2shoprelationssqlgenerator',
                                         'oxaccessrightexception',
                                         'oxexpirationemailbuilder',
                                         'oxldap',
                                         'oxserial',];


    /** @var null|EditionSelector An instance of the edition selector */
    private $editionSelector = null;

    /** @var null|string The path to the project root directory */
    private $projectRootDirectory = null;

    /**
     * Generator constructor.
     *
     * @param string $projectRootDirectory Project root directory
     */
    public function __construct($projectRootDirectory)
    {
        $this->editionSelector = new EditionSelector();

        $this->projectRootDirectory = $projectRootDirectory;
    }

    /**
     * Generate a helper file for IDE auto-completion
     */
    public function generate()
    {
        $output = '';
        $edition = $this->getEdition();

        if ($edition == \OxidEsales\Eshop\Core\Edition\EditionSelector::COMMUNITY) {
            $classMap = $this->getMapCommunity();
            $output = $this->generateIdeHelperOutput($classMap);
        }
        if ($edition == \OxidEsales\Eshop\Core\Edition\EditionSelector::PROFESSIONAL) {
            $classMap = $this->getMapProfessional();
            $output = $this->generateIdeHelperOutput($classMap);
        }
        if ($edition == \OxidEsales\Eshop\Core\Edition\EditionSelector::ENTERPRISE) {
            $classMap = $this->getMapEnterprise();
            $output = $this->generateIdeHelperOutput($classMap);
        }

        file_put_contents($this->projectRootDirectory . '/.ide-helper.php', $output);
    }

    /**
     * Generate the helper classes for a given class map
     *
     * @param array $classMap
     *
     * @return mixed|string
     */
    protected function generateIdeHelperOutput(array $classMap)
    {
        $virtualNamespaces = [];

        $nameSpaces = $this->getNameSpaces($classMap);
        foreach ($nameSpaces as $nameSpace => $reflectionObjects) {
            $virtualNamespace = str_replace(
                ['Community', 'Professional', 'Enterprise'],
                ['', '', ''],
                $nameSpace
            );
            /** @var \ReflectionObject $reflectionObject */
            foreach ($reflectionObjects as $reflectionObject) {
                $virtualNamespaces[$virtualNamespace][] = [
                    // Interfaces are abstract for Reflection too, here we want just abstract classes
                    'isAbstract'      => $reflectionObject->isAbstract() && !$reflectionObject->isInterface(),
                    'isInterface'     => $reflectionObject->isInterface(),
                    'childClassName'  => $reflectionObject->getShortName(),
                    'parentClassName' => $reflectionObject->getName(),
                ];
            }
        }

        $backwardsCompatibleClasses = [];
        $backwardsCompatibilityMap = $this->getBackwardsCompatibilityMap();
        $backwardsCompatibleReflectionObjects = $this->getBackwardsCompatibleReflectionObjects($backwardsCompatibilityMap);
        foreach ($backwardsCompatibleReflectionObjects as $backwardsCompatibleClassName => $reflectionObject) {
            $virtualClassName = str_replace(
                ['Community', 'Professional', 'Enterprise'],
                ['', '', ''],
                $reflectionObject->getName()
            );
            $backwardsCompatibleClasses[] = [
                // Interfaces are abstract for Reflection too, here we want just abstract classes
                'isAbstract'      => $reflectionObject->isAbstract() && !$reflectionObject->isInterface(),
                'isInterface'     => $reflectionObject->isInterface(),
                'childClassName'  => $backwardsCompatibleClassName,
                'parentClassName' => $virtualClassName,
            ];
        }

        $smarty = $this->getSmarty();
        $smarty->assign('nameSpaces', $virtualNamespaces);
        $smarty->assign('backwardsCompatibleClasses', $backwardsCompatibleClasses);
        $output = $smarty->fetch('main-template.tpl');

        return $output;
    }

    /**
     * Handle a given exception
     *
     * @param \Exception $exception
     */
    protected function handleException(\Exception $exception)
    {
        echo 'Warning ' . $exception->getMessage() . PHP_EOL;
    }

    /**
     * Handle a given exception
     *
     * @param \Exception $exception
     */
    protected function handleBackwardsCompatibleClassException(\Exception $exception)
    {
        /** There are some expected missing classes in OXID eSales Community Edition */
        preg_match('/Class (.*?) does not exist/i', $exception->getMessage(), $matches);
        if (isset($matches[1]) && in_array($matches[1], $this->bcExpectedMissingClasses)) {
            return;
        }
        echo 'Warning ' . $exception->getMessage() . PHP_EOL;
    }

    /**
     * Return the currently installed edition of OXID eSales eShop
     *
     * @return string
     */
    protected function getEdition()
    {
        return $this->editionSelector->getEdition();
    }

    /**
     * Return the VirtualNameSpaceClassMap of OXID eSales eShop Community Edition
     *
     * @return array
     */
    protected function getMapCommunity()
    {
        $classMap = [];

        if (class_exists(\OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMap::class)) {
            $virtualNameSpaceClassMap = new \OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMap();
            $classMap = $virtualNameSpaceClassMap->getClassMap();
        }

        return $classMap;
    }

    /**
     * Return the VirtualNameSpaceClassMap of OXID eSales eShop Professional Edition
     *
     * @return array
     */
    protected function getMapProfessional()
    {
        $classMap = [];

        if (class_exists(\OxidEsales\EshopProfessional\Core\Autoload\VirtualNameSpaceClassMap::class)) {
            $virtualNameSpaceClassMap = new \OxidEsales\EshopProfessional\Core\Autoload\VirtualNameSpaceClassMap();
            $classMap = $virtualNameSpaceClassMap->getClassMap();
        }

        return $classMap;
    }

    /**
     * Return the VirtualNameSpaceClassMap of OXID eSales eShop Enterprise Edition
     *
     * @return array
     */
    protected function getMapEnterprise()
    {
        $classMap = [];

        if (class_exists(\OxidEsales\EshopEnterprise\Core\Autoload\VirtualNameSpaceClassMap::class)) {
            $virtualNameSpaceClassMap = new \OxidEsales\EshopEnterprise\Core\Autoload\VirtualNameSpaceClassMap();
            $classMap = $virtualNameSpaceClassMap->getClassMap();
        }

        return $classMap;
    }

    /**
     * Get the virtual namespaces and the associated ReflectionClasses of the mapped classes
     *
     * @param array $classMap Mapping of classes in a virtual namespace real existing classes
     *
     * @return array The namespaces and their associated ReflectionClasses.
     */
    protected function getNameSpaces($classMap)
    {
        $nameSpaces = [];
        foreach ($classMap as $virtualClass => $concreteClass) {
            try {
                $reflectionObject = new ReflectionClass($concreteClass);
                $nameSpaces[$reflectionObject->getNamespaceName()][] = $reflectionObject;
            } catch (ReflectionException $exception) {
                $this->handleException($exception);
            }
        }

        return $nameSpaces;
    }


    /**
     * Get the backwards compatible classes and the associated ReflectionClasses of the mapped classes
     *
     * @param array $classMap Mapping of backwards compatible class names to virtual class names
     *
     * @return array The backwards compatible classes their associated ReflectionClasses.
     */
    protected function getBackwardsCompatibleReflectionObjects($classMap)
    {
        $backwardsCompatibleClasses = [];
        foreach ($classMap as $backwardsCompatibleClassName => $virtualClassName) {
            try {
                $reflectionObject = new ReflectionClass($backwardsCompatibleClassName);
                $backwardsCompatibleClasses[$backwardsCompatibleClassName] = $reflectionObject;
            } catch (ReflectionException $exception) {
                $this->handleBackwardsCompatibleClassException($exception);
            }
        }

        return $backwardsCompatibleClasses;
    }

    /**
     * @return array
     */
    public function getBackwardsCompatibilityMap()
    {
        $backwardsCompatibilityMap = array_flip(
            include $this->projectRootDirectory . DIRECTORY_SEPARATOR .
                    'source' . DIRECTORY_SEPARATOR .
                    'Core' . DIRECTORY_SEPARATOR .
                    'Autoload' . DIRECTORY_SEPARATOR .
                    'BackwardsCompatibilityClassMap.php'
        );

        return $backwardsCompatibilityMap;
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
}
