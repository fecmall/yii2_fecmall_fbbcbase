<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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
