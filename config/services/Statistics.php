<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
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