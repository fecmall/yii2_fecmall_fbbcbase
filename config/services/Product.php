<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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
