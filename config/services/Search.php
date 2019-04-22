<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
return [
    'search' => [
        // 搜索将所有的sku都展示出来。
        'productSpuShowOnlyOneSku' => false,
        'childService' => [
            'mongoSearch' => [
                'class'        => 'fbbcbase\services\search\MongoSearch',
            ],
            'xunSearch' => [
                'class'        => 'fbbcbase\services\search\XunSearch',
            ],
        ],
    ],
];
