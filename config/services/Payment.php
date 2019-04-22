<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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
