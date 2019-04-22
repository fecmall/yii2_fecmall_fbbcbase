<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
return [
    'order' => [
        'class' => 'fbbcbase\services\Order',
        'childService' => [
            'item' => [
                'class' => 'fbbcbase\services\order\Item',
            ],
            'info' => [
                'class' => 'fbbcbase\services\order\Info',
            ],
            'process' => [
                'class' => 'fbbcbase\services\order\Process',
            ],
            'afterSale' => [
                'class' => 'fbbcbase\services\order\AfterSale',
            ],
            'processLog' => [
                'class' => 'fbbcbase\services\order\ProcessLog',
            ],
        ],
    ],
];