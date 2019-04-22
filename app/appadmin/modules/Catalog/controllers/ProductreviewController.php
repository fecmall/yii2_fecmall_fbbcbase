<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appadmin\modules\Catalog\controllers;

use fecshop\app\appadmin\modules\Catalog\CatalogController;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ProductreviewController extends \fecshop\app\appadmin\modules\Catalog\controllers\ProductreviewController
{
    public $enableCsrfValidation = true;
    public $blockNamespace = 'fbbcbase\\app\\appadmin\\modules\\Catalog\\block';
    

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

    public function actionManagereditsave()
    {
        $data = $this->getBlock('manageredit')->save();
    }

    public function actionManagerdelete()
    {
        $this->getBlock('manageredit')->delete();
    }

    public function actionManageraudit()
    {
        $this->getBlock('manageredit')->audit();
    }

    public function actionManagerauditrejected()
    {
        $this->getBlock('manageredit')->auditRejected();
    }
}
