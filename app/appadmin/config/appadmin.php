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
        'i18n' => [
            'translations' => [
                'appadmin' => [
                    'basePaths' => [
                        '@fbbcbase/app/appadmin/languages',
                    ],
                ],
            ],
        ],
    ],
];
