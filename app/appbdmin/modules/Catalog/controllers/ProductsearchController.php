<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Catalog\controllers;

use fbbcbase\app\appbdmin\modules\Catalog\CatalogController;
use Yii;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ProductsearchController extends CatalogController
{
    public $enableCsrfValidation = true;
    
    public function actionIndex()
    {
        echo Yii::$service->page->translate->__('not develop');
    }
}
