<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
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