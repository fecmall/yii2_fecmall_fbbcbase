<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'product' => [
        'storagePath' => 'fbbcbase\\services\\product',
        'productSpuShowOnlyOneSku' => false,
        'childService' => [
            'review' => [
                'class' => 'fbbcbase\services\product\Review',
            ],
            'favorite' => [
                'class' => 'fbbcbase\services\product\Favorite',
            ],
            'stock' => [
                'class' => 'fbbcbase\services\product\Stock',
            ],
        ],
    ],
];
