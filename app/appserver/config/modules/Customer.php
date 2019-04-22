<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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