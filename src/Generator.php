<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
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


    public function __construct($projectRootDirectory)
    {
        $this->editionSelector = new EditionSelector();

        $this->projectRootDirectory = $projectRootDirectory;
    }

    /**
     * Generate a helper file for IDE aut-completion
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

        return $this->generateIdeHelperClasses($classMapCommunity);
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

        return $this->generateIdeHelperClasses($classMapProfessional);
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

        return $this->generateIdeHelperClasses($classMapEnterprise);
    }


    /**
     * Generate the helper classes for a given class map
     *
     * @param array $classMap
     *
     * @return mixed|string|void
     */
    protected function generateIdeHelperClasses(array $classMap)
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
                    'constants'           => [], // $this->getConstantsReflectionObject($reflectionObject),
                    'privateProperties'   => [], //  $this->getPrivatePropertiesReflectionObject($reflectionObject),
                    'protectedProperties' => [], //  $this->getProtectedPropertiesReflectionObject($reflectionObject),
                    'publicProperties'    => [], //  $this->getPublicPropertiesReflectionObject($reflectionObject),
                    'privateMethods'      => [], //  $this->getPrivateMethodsReflectionObject($reflectionObject),
                    'protectedMethods'    => [], //  $this->getProtectedMethodsReflectionObject($reflectionObject),
                    'publicMethods'       => [], //  $this->getPublicMethodsReflectionObject($reflectionObject),
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
     * Return the VirtualNameSpaceClassMap of the OXID eSales eShop Community Edition
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
     * Return the VirtualNameSpaceClassMap of the OXID eSales eShop Professional Edition
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
     * Return the VirtualNameSpaceClassMap of the OXID eSales eShop Enterprise Edition
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
        $smarty->config_dir = realpath($currentDirectory . '/smarty/configs/');
        $smarty->cache_dir = realpath($currentDirectory . '/smarty/cache/');
        $smarty->left_delimiter = '{{';
        $smarty->right_delimiter = '}}';
        $smarty->debugging = true;

        return $smarty;
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     *
     * @return array
     */
    protected function getConstantsReflectionObject(ReflectionClass $reflectionObject)
    {
        $constants = [];
        foreach ($reflectionObject->getConstants() as $name => $value) {
            $constants[] = [
                'name'     => $name,
                'docBlock' => '',
                'value'    => $value,
            ];
        }

        return $constants;
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     *
     * @return array
     */
    protected function getPrivatePropertiesReflectionObject(ReflectionClass $reflectionObject)
    {
        $filter = ReflectionProperty::IS_PRIVATE;

        return $this->getProperties($reflectionObject, $filter);
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     *
     * @return array
     */
    protected function getProtectedPropertiesReflectionObject(ReflectionClass $reflectionObject)
    {
        $filter = ReflectionProperty::IS_PROTECTED;

        return $this->getProperties($reflectionObject, $filter);
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     *
     * @return array
     */
    protected function getPublicPropertiesReflectionObject(ReflectionClass $reflectionObject)
    {
        $filter = ReflectionProperty::IS_PUBLIC;

        return $this->getProperties($reflectionObject, $filter);
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     *
     * @return array
     */
    protected function getPrivateMethodsReflectionObject(ReflectionClass $reflectionObject)
    {
        $filter = \ReflectionMethod::IS_PRIVATE;

        $methods = $this->getMethods($reflectionObject, $filter);

        return $methods;
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     *
     * @return array
     */
    protected function getProtectedMethodsReflectionObject(ReflectionClass $reflectionObject)
    {
        $filter = \ReflectionMethod::IS_PROTECTED;

        $methods = $this->getMethods($reflectionObject, $filter);

        return $methods;
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     *
     * @return array
     */
    protected function getPublicMethodsReflectionObject(ReflectionClass $reflectionObject)
    {
        $filter = \ReflectionMethod::IS_PUBLIC;

        $methods = $this->getMethods($reflectionObject, $filter);

        return $methods;
    }

    /**
     * Not used ATM
     *
     * @param ReflectionParameter $parameter
     *
     * @return string|null
     */
    protected function getParameterType(ReflectionParameter $parameter)
    {
        $export = ReflectionParameter::export(
            array(
                $parameter->getDeclaringClass()->name,
                $parameter->getDeclaringFunction()->name
            ),
            $parameter->name,
            true
        );

        $type = preg_match('/[>] ([A-z]+) /', $export, $matches)
            ? $matches[1] : null;

        if (false !== strpos($type, '\\')) {
            $type = '\\' . ltrim($type, '\\');
        }

        return $type;
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     * @param                 $filter
     *
     * @return array
     */
    protected function getProperties(ReflectionClass $reflectionObject, $filter)
    {
        $defaultProperties = $reflectionObject->getDefaultProperties();
        $staticProperties = $reflectionObject->getStaticProperties();
        $properties = [];
        foreach ($reflectionObject->getProperties($filter) as $reflectionProperty) {
            $declaringClass = $reflectionProperty->getDeclaringClass();
            if ($declaringClass->getShortName() == $reflectionObject->getShortName()) {
                $propertyValue = var_export($defaultProperties[$reflectionProperty->getName()], true);
                if ($reflectionProperty->isStatic()) {
                    $propertyName = $reflectionProperty->getName();
                    // TODO this is not working as documented and throws an exception ATM
                    // $propertyValue = var_export($reflectionObject->getStaticPropertyValue($propertyName), true);
                }
                $properties[] = [
                    'name'     => $reflectionProperty->getName(),
                    'isStatic' => $reflectionProperty->isStatic(),
                    'docBlock' => $reflectionProperty->getDocComment(),
                    'value'    => $propertyValue
                ];
            }
        }

        return $properties;
    }

    /**
     * Not used ATM
     *
     * @param ReflectionClass $reflectionObject
     * @param                 $filter
     *
     * @return array
     */
    protected function getMethods(ReflectionClass $reflectionObject, $filter)
    {
        $methods = [];
        foreach ($reflectionObject->getMethods($filter) as $method) {
            $declaringClass = $method->getDeclaringClass();
            if ($declaringClass->getShortName() == $reflectionObject->getShortName()) {
                $reflectionParameters = $method->getParameters();
                $parameters = [];
                foreach ($reflectionParameters as $reflectionParameter) {
                    $parameters[$reflectionParameter->getPosition()] = [
                        'type'            => $this->getParameterType($reflectionParameter) ? $this->getParameterType($reflectionParameter) : '',
                        'name'            => $reflectionParameter->getName(),
                        'hasDefaultValue' => $reflectionParameter->isDefaultValueAvailable(),
                        'defaultValue'    => $reflectionParameter->isDefaultValueAvailable() ? var_export($reflectionParameter->getDefaultValue(), true) : '',
                        'byReference'     => $reflectionParameter->isPassedByReference()
                    ];
                }
                ksort($parameters);


                $methods[] = [
                    'name'       => $method->getName(),
                    'isStatic'   => $method->isStatic(),
                    'isAbstract' => $method->isAbstract() && !$reflectionObject->isInterface(),
                    'docBlock'   => $method->getDocComment(),
                    'parameters' => $parameters,
                ];
            }
        }
        sort($methods);

        return $methods;
    }
}
