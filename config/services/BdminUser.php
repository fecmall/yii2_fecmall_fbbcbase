<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'bdminUser' => [
        'class' => 'fbbcbase\services\BdminUser',
        'childService' => [
            'bdminUser' => [
                'class' => 'fbbcbase\services\bdminUser\BdminUser',
            ],
            'userLogin' => [
                'class' => 'fbbcbase\services\bdminUser\UserLogin',
            ],
        ],
    ],
];
