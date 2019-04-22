<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Catalog\helper;

use Yii;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
// use fbbcbase\app\appbdmin\modules\Catalog\helper\Product as ProductHelper;
class Product
{
    public static function getStatusArr()
    {
        return [
            1 => Yii::$service->page->translate->__('Enable'),
            2 => Yii::$service->page->translate->__('Disable'),
        ];
    }

    public static function getInStockArr()
    {
        return [
            1 => Yii::$service->page->translate->__('In stock'),
            2 => Yii::$service->page->translate->__('out of stock'),
        ];
    }
}
