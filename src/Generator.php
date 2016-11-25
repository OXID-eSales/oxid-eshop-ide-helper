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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 */

namespace OxidEsales\EshopIdeHelper;

use OxidEsales\Eshop\Core\Edition\EditionSelector;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Class Generator
 *
 * @package OxidEsales\EshopIdeHelper
 */
class Generator
{

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
            $output = $this->generateCommunity();
        }
        if ($edition == \OxidEsales\Eshop\Core\Edition\EditionSelector::PROFESSIONAL) {
            $output = $this->generateProfessional();
        }
        if ($edition == \OxidEsales\Eshop\Core\Edition\EditionSelector::ENTERPRISE) {
            $output = $this->generateEnterprise();
        }

        file_put_contents($this->projectRootDirectory . '/.ide-helper.php', $output);
    }

    /**
     * Generate the output for the OXID eSales eShop Community Edition
     *
     * @return mixed|string|void
     */
    protected function generateCommunity()
    {

        $virtualNameSpaceClassMapCommunity = $this->getMapCommunity();
        $classMapCommunity = $virtualNameSpaceClassMapCommunity->getOverridableMap();

        return $this->generateIdeHelperOutput($classMapCommunity);
    }

    /**
     * Generate the output for the OXID eSales eShop Professional Edition
     *
     * @return mixed|string|void
     */
    protected function generateProfessional()
    {
        $virtualNameSpaceClassMapCommunity = $this->getMapCommunity();
        $virtualNameSpaceClassMapProfessional = $this->getMapProfessional();

        $classMapCommunity = $virtualNameSpaceClassMapCommunity->getOverridableMap();
        $classMapProfessional = array_merge($classMapCommunity, $virtualNameSpaceClassMapProfessional->getOverridableMap());

        return $this->generateIdeHelperOutput($classMapProfessional);
    }

    /**
     * Generate the output for the OXID eSales eShop Enterprise Edition
     *
     * @return mixed|string|void
     */
    protected function generateEnterprise()
    {
        $virtualNameSpaceClassMapCommunity = $this->getMapCommunity();
        $virtualNameSpaceClassMapProfessional = $this->getMapProfessional();
        $virtualNameSpaceClassMapEnterprise = $this->getMapEnterprise();

        $classMapCommunity = $virtualNameSpaceClassMapCommunity->getOverridableMap();
        $classMapProfessional = array_merge($classMapCommunity, $virtualNameSpaceClassMapProfessional->getOverridableMap());
        $classMapEnterprise = array_merge($classMapProfessional, $virtualNameSpaceClassMapEnterprise->getOverridableMap());

        return $this->generateIdeHelperOutput($classMapEnterprise);
    }


    /**
     * Generate the helper classes for a given class map
     *
     * @param array $classMap
     *
     * @return mixed|string|void
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
            foreach ($reflectionObjects as $reflectionObject) {
                $virtualNamespaces[$virtualNamespace][] = [
                    // Interfaces are abstract for Reflection too, here we want just abstract classes
                    'isAbstract'          => $reflectionObject->isAbstract() && !$reflectionObject->isInterface(),
                    'isInterface'         => $reflectionObject->isInterface(),
                    'fullClassName'       => $reflectionObject->getName(),
                    'shortClassName'      => $reflectionObject->getShortName(),
                ];
            }
        }

        $smarty = $this->getSmarty();
        $smarty->assign('nameSpaces', $virtualNamespaces);
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
     * @return null|\OxidEsales\EshopCommunity\Core\Edition\ClassMap
     */
    protected function getMapCommunity()
    {
        $classMap = null;

        if (class_exists(\OxidEsales\EshopCommunity\Core\VirtualNameSpaceClassMap::class)) {
            $classMap = new \OxidEsales\EshopCommunity\Core\VirtualNameSpaceClassMap();
        }

        return $classMap;
    }

    /**
     * Return the VirtualNameSpaceClassMap of OXID eSales eShop Professional Edition
     *
     * @return null|\OxidEsales\EshopCommunity\Core\Edition\ClassMap
     */
    protected function getMapProfessional()
    {
        $classMap = null;

        if (class_exists(\OxidEsales\EshopProfessional\Core\VirtualNameSpaceClassMap::class)) {
            $classMap = new \OxidEsales\EshopProfessional\Core\VirtualNameSpaceClassMap();
        }

        return $classMap;
    }

    /**
     * Return the VirtualNameSpaceClassMap of OXID eSales eShop Enterprise Edition
     *
     * @return null|\OxidEsales\EshopCommunity\Core\Edition\ClassMap
     */
    protected function getMapEnterprise()
    {
        $classMap = null;

        if (class_exists(\OxidEsales\EshopEnterprise\Core\VirtualNameSpaceClassMap::class)) {
            $classMap = new \OxidEsales\EshopEnterprise\Core\VirtualNameSpaceClassMap();
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
     * Return an instance of smarty
     *
     * @return \Smarty
     */
    protected function getSmarty()
    {
        $smarty = new \Smarty();
        $currentDirectory = dirname(__FILE__);
        $smarty->template_dir = realpath($currentDirectory . '/smarty/templates/');
        $smarty->compile_dir = realpath($currentDirectory . '/smarty/templates_c/');
        $smarty->left_delimiter = '{{';
        $smarty->right_delimiter = '}}';

        return $smarty;
    }
}
