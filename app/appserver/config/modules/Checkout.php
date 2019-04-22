<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'checkout' => [
        'controllerMap' => [
            'cart' => 'fbbcbase\app\appserver\modules\Checkout\controllers\CartController',  
            'onepage' => 'fbbcbase\app\appserver\modules\Checkout\controllers\OnepageController', 
            'payment' => 'fbbcbase\app\appserver\modules\Checkout\controllers\PaymentController',             
        ],
        'params'=> [
            'guestOrder' => false, // 是否支持游客下单
            'guestCart' => false, // 是否支持游客下单
        ],
    ]
];