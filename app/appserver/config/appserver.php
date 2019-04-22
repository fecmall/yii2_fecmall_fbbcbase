<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
// 本文件在app/web/index.php 处引入。
// fecshop - appfront 的核心模块
$modules = [];
foreach (glob(__DIR__ . '/modules/*.php') as $filename) {
    $modules = array_merge($modules, require($filename));
}
// 此处也可以重写fecshop的组件。供调用。
$config = [
    'modules'=>$modules,
    /* only config in front web */
    'params'    => [
    ],
    // language config.
    'components' => [
        'i18n' => [
            'translations' => [
                'appserver' => [
                    'basePaths' => [
                        '@fbbcbase/app/appserver/languages',
                    ],
                    'sourceLanguage' => 'en_US', // 如果 en_US 也想翻译，那么可以改成en_XX。
                ],
            ],
        ],
    ],

];

return $config;