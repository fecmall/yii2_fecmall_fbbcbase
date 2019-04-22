<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'helper' => [
        'class' => 'fbbcbase\services\Helper',
        'childService' => [
            'appserver' => [
                'class' => 'fbbcbase\services\helper\Appserver',
            ],
        ],
    ],
];