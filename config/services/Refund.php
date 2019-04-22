<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'refund' => [
        'class' => 'fbbcbase\services\Refund',
        'childService' => [
            'orderCancel' => [
                'class' => 'fbbcbase\services\refund\OrderCancel',
            ],
            'orderReturn' => [
                'class' => 'fbbcbase\services\refund\OrderReturn',
            ],
        
        
        ]
    ],
];