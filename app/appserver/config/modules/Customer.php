<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'customer' => [
        'controllerMap' => [
            'order' => 'fbbcbase\app\appserver\modules\Customer\controllers\OrderController',       
            'address' => 'fbbcbase\app\appserver\modules\Customer\controllers\AddressController',                       
            'register' => 'fbbcbase\app\appserver\modules\Customer\controllers\RegisterController',       
            'login' => 'fbbcbase\app\appserver\modules\Customer\controllers\LoginController',       
            
        ],
        'params'=> [
            'register' => [
                // 账号注册成功后，是否自动登录
                'successAutoLogin' => true,

            ],
        ],
    ]
];