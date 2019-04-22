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
    'params'    => $params,
    'components' => [
        'user' => [
            // 【默认】不开启速度限制的 User Model
            'identityClass' => 'fbbcbase\models\mysqldb\BdminUser',
            // 开启速度限制的 User Model
            //'identityClass' => 'fecshop\models\mysqldb\adminUser\AdminUserAccessToken',
            
            //'enableAutoLogin' => true,
            // 关闭session
            'enableSession'     => false,
        ],
    ],
];
