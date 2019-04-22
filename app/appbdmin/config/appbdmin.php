<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
// 本文件在app/web/index.php 处引入。
// fecshop的核心模块
$modules = [];
foreach (glob(__DIR__ . '/modules/*.php') as $filename) {
    $modules = array_merge($modules, require($filename));
}
$params = require __DIR__ .'/params.php';
return [
    'modules'=>$modules,
    /* only config in front web */
    'bootstrap' => ['store'],
    'params'    => $params,
    'components' => [
        'store' => [
            'appName' => 'appbdmin',
        ],
        'user' => [
            'identityClass' => 'fbbcbase\models\mysqldb\BdminUser',
            'enableAutoLogin' => true,
        ],
        'i18n' => [
            'translations' => [
                'appbdmin' => [
                    //'class' => 'yii\i18n\PhpMessageSource',
                    'class' => 'fecshop\yii\i18n\PhpMessageSource',
                    'basePaths' => [
                        '@fbbcbase/app/appbdmin/languages',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'fecadmin/error',
        ],

        'urlManager' => [
            'rules' => [
                '' => 'fecbdmin/index/index',
            ],
        ],
    ],
    
];
