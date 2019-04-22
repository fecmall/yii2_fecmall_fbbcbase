<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
namespace fbbcbase\app\appapi\modules\V2;

use fecshop\app\appapi\modules\AppapiModule;
use Yii;

class Module extends AppapiModule
{
    public $blockNamespace;

    public function init()
    {
        // 以下代码必须指定
        $nameSpace = __NAMESPACE__;
        // web controller
        $this->controllerNamespace = $nameSpace . '\\controllers';
        $this->blockNamespace = $nameSpace . '\\block';
        // 指定默认的man文件
        //$this->layout = "home.php";
        //Yii::$service->page->theme->layoutFile = 'home.php';
        parent::init();
    }
}
