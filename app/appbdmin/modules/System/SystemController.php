<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fecshop\app\appadmin\modules\System;

/*
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
use fecadmin\FecadminbaseController;
use Yii;
use fecshop\app\appadmin\modules\AppadminController;

class SystemController extends AppadminController
{
    public $enableCsrfValidation = false;
    
    //public function getViewPath()
    //{
    //    return Yii::getAlias('@fecshop/app/appadmin/modules/System/views') . DIRECTORY_SEPARATOR . $this->id;
    //}
}
