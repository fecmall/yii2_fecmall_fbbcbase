<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appadmin\modules\Supplier;

/*
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
use fecadmin\FecadminbaseController;
use Yii;
use fecshop\app\appadmin\modules\AppadminController;

class SupplierController extends AppadminController
{
    public $enableCsrfValidation = false;
    
    //public function getViewPath()
    //{
    //    return Yii::getAlias('@fecshop/app/appadmin/modules/Customer/views') . DIRECTORY_SEPARATOR . $this->id;
    //}
}
