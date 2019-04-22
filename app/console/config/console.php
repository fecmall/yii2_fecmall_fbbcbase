<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
?>
<?php
$modules = [];
foreach (glob(__DIR__ . '/modules/*.php') as $filename) {
    $modules = array_merge($modules, require($filename));
}

return [
    'modules'=>$modules,
    'params' => [
        
    ],
];
