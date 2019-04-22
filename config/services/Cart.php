<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
return [
    'cart' => [
        'class' => 'fbbcbase\services\Cart',
        // 子服务
        'childService' => [
            'info' => [
                'class' => 'fbbcbase\services\cart\Info',
            ],
            'quote' => [
                'class' => 'fbbcbase\services\cart\Quote',
            ],
            'quoteItem' => [
                'class' => 'fbbcbase\services\cart\QuoteItem',
            ],
        ],
    ],
];
