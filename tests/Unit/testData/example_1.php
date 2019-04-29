<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$aModule = [
    'extend' => [
        \OxidEsales\Eshop\Core\Header::class        => \OxidEsales\TestModule\Core\Header::class,
        \OxidEsales\Eshop\Core\ShopControl::class   => \OxidEsales\TestModule\Core\ShopControl::class,
        \OxidEsales\Eshop\Core\WidgetControl::class => \OxidEsales\TestModule\Core\WidgetControl::class,
    ]
];
