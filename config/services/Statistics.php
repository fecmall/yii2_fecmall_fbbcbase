<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
return [
    'statistics' => [
        'class' => 'fbbcbase\services\Statistics',
        'childService' => [
            'order' => [
                'class' => 'fbbcbase\services\statistics\Order',
            ],
            'bdminMonth' => [
                'class' => 'fbbcbase\services\statistics\BdminMonth',
            ],
        ],
    ],
];