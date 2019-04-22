<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fecshop\app\appadmin\modules\System\controllers;

use fecshop\app\appadmin\modules\System\SystemController;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ErrorController extends SystemController
{
    public $enableCsrfValidation = false;
    
    public function actionIndex()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }

    public function actionManageredit()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }
}
