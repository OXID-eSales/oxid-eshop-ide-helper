<?php
/**
 * This file is part of OXID eSales OXID eShop Facts.
 *
 * OXID eSales OXID eShop Facts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales OXID eShop Facts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales OXID eShop Facts. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

namespace OxidEsales\Facts\Edition;

use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Facts;

/**
 * Class is responsible for returning edition of OXID eShop.
 */
class EditionSelector
{

    const ENTERPRISE = 'EE';

    const PROFESSIONAL = 'PE';

    const COMMUNITY = 'CE';

    /** @var string Edition abbreviation */
    private $edition = null;

    /** @var ConfigFile */
    private $configFile = null;

    /**
     * EditionSelector constructor.
     * Adds possibility to inject ConfigFile to force different settings.
     *
     * @param null|ConfigFile $configFile
     */
    public function __construct($configFile = null)
    {
        $this->configFile = $configFile;

        $this->edition = $this->findEdition();
    }

    /**
     * Method returns edition.
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * @return bool
     */
    public function isEnterprise()
    {
        return $this->getEdition() === static::ENTERPRISE;
    }

    /**
     * @return bool
     */
    public function isProfessional()
    {
        return $this->getEdition() === static::PROFESSIONAL;
    }

    /**
     * @return bool
     */
    public function isCommunity()
    {
        return $this->getEdition() === static::COMMUNITY;
    }

    /**
     * Check for forced edition in config file. If edition is not specified,
     * determine it by ClassMap existence.
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function findEdition()
    {
        try {
            $edition = $this->findEditionByConfigFile();
            if (empty($edition)) {
                $edition = $this->findEditionByEditionFiles();
            }
        } catch (\Exception $exception) {
            try {
                $edition = $this->findEditionByEditionFiles();
            } catch (\Exception $exception) {
                throw $exception;
            }
        }

        return strtoupper($edition);
    }


    /**
     * Find edition by directories of the editions in the vendor directory
     *
     * @return string
     *
     * @throws \Exception
     */
    private function findEditionByEditionFiles()
    {
        $facts = $this->getFacts();
        $edition = '';
        if (is_dir($facts->getEnterpriseEditionRootPath()) === true) {
            $edition = static::ENTERPRISE;
        } elseif (is_dir($facts->getProfessionalEditionRootPath()) === true) {
            $edition = static::PROFESSIONAL;
        } elseif (is_dir($facts->getCommunityEditionSourcePath()) === true) {
            $edition = static::COMMUNITY;
        }

        if ($edition === '') {
            throw new \Exception("Shop directory structure is not setup properly. Edition could not be detected");
        }

        return $edition;
    }

    /**
     * @return Facts
     */
    private function getFacts()
    {
        return new Facts();
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function findEditionByConfigFile()
    {
        $configFile = $this->getConfigFile();
        $edition = $configFile->getVar('edition');

        return $edition;
    }

    /**
     * Safeguard for ConfigFile object.
     *
     * @return null|ConfigFile
     */
    protected function getConfigFile()
    {
        if (is_null($this->configFile)) {
            $this->configFile = new ConfigFile();
        }

        return $this->configFile;
    }
}
