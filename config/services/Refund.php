<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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