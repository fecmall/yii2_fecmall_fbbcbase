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
        'controllerMap' => [
            'checkmoney' => 'fbbcbase\app\appserver\modules\Payment\controllers\CheckmoneyController',     
            'success' => 'fbbcbase\app\appserver\modules\Payment\controllers\SuccessController',              
        ],
    ]
];