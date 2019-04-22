<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
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
