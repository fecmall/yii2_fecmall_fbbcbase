<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'customer' => [
        'class' => 'fbbcbase\services\Customer',
        // 子服务
        'childService' => [
            'address' => [
                'class' => 'fbbcbase\services\customer\Address',
            ],
        ],
    ],
];
