<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
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
