<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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