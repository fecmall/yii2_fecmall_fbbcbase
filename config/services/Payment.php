<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'payment' => [
        'class' => 'fbbcbase\services\Payment',
        'childService' => [
            'paypal' => [
                'class'    => 'fbbcbase\services\payment\Paypal',
            ],
            'alipay' => [
                'class'         => 'fbbcbase\services\payment\Alipay',
            ],
        ],
    ],
];
