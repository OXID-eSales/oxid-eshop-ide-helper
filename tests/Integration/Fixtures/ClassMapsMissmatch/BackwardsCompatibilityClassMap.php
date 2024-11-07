<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

return [
    'oxClassExistsInCommunityEdition' => 'OxidEsales\\Eshop\\ClassExistsInCommunityEdition',
    /**
     * ClassExistsInEnterpriseEdition is missing from UnifiedNamespaceClassMap.php
     * Use case: we want to generate the ide-helper for the CE edition so
     * EE classes are not present in the UnifiedNamespaceClassMap.php
     * */
    'oxClassExistsInEnterpriseEdition' => 'OxidEsales\\Eshop\\ClassExistsInEnterpriseEdition'
];
