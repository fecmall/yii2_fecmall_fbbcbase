<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appadmin\modules\Cms\controllers;

use fecshop\app\appadmin\modules\Cms\CmsController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BaseinfoController extends CmsController
{
    public $enableCsrfValidation = true;
    public $blockNamespace = 'fbbcbase\\app\\appadmin\\modules\\Cms\\block';
    
    
    public function actionManager()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }
    
    
    public function actionManagereditsave()
    {
        $data = $this->getBlock('manager')->save();
    }
    
    
    
    
}
