<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appbdmin\modules\Sales\controllers;

use fbbcbase\app\appbdmin\modules\Sales\SalesController;
use Yii;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class OrderlogController extends SalesController
{
    public $enableCsrfValidation = true;
    
    public function actionManager()
    {
        $data = $this->getBlock()->getLastData();
    
        return $this->render($this->action->id, $data);
    }
    
    public function actionManageredit()
    {
        $primaryKey = Yii::$service->statistics->bdminMonth->getPrimaryKey();
        $m_id = Yii::$app->request->get($primaryKey);
        $this->orderHasRole($m_id);
        
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }
    
    
    /**
     * 当前用户是否有操作该产品的权限
     */
    public function orderHasRole($id){
        $bdminMonth = Yii::$service->statistics->bdminMonth->getByPrimaryKey($id);
        $af_bdmin_user_id = isset($bdminMonth['bdmin_user_id']) ? $bdminMonth['bdmin_user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$af_bdmin_user_id || $currentUserId != $af_bdmin_user_id) {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to view this month statistics'),
            ]);
            exit;
        }
        unset($bdminMonth);
    }
    
}
