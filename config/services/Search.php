<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
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
