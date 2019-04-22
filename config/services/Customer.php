<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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
