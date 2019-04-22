<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
// 本文件在app/web/index.php 处引入。
// 服务
$services = [];
foreach (glob(__DIR__ . '/services/*.php') as $filename) {
    $services = array_merge($services, require($filename));
}

// 组件
$components = [];
foreach (glob(__DIR__ . '/components/*.php') as $filename) {
    $components = array_merge($components, require($filename));
}

return [
    'components'    => $components,
    'services'        => $services,
    'params'        => [

    ],
];
