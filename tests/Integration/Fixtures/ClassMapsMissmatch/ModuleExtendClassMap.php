<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

return [
    [
        'isAbstract' => false,
        'isInterface' => false,
        'childClassName' => 'User_parent',
        'parentClassName' => "OxidEsales\Eshop\Application\Model\User",
        'namespace' => 'OxidEsales\TestModule\Model'
    ],
    [
        'isAbstract' => false,
        'isInterface' => false,
        'childClassName' => 'Article_parent',
        'parentClassName' => "OxidEsales\Eshop\Application\Model\Article",
        'namespace' => 'OxidEsales\TestModule\Model'
    ],
];
