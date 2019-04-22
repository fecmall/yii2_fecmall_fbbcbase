<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'sales' => [
        'controllerMap' => [
            'orderinfo' => 'fbbcbase\app\appadmin\modules\Sales\controllers\OrderinfoController',      
            'ordersettle' => 'fbbcbase\app\appadmin\modules\Sales\controllers\OrdersettleController',                    
            'returnlist' => 'fbbcbase\app\appadmin\modules\Sales\controllers\ReturnlistController',          
            'refund' => 'fbbcbase\app\appadmin\modules\Sales\controllers\RefundController',      
            'refundbdmin' => 'fbbcbase\app\appadmin\modules\Sales\controllers\RefundbdminController',                 
            'orderlog' => 'fbbcbase\app\appadmin\modules\Sales\controllers\OrderlogController',      
            
        ],
    ],
];