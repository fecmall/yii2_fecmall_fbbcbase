
<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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