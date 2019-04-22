<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appbdmin\modules\Cms\controllers;

use fbbcbase\app\appbdmin\modules\Cms\CmsController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BaseinfoController extends CmsController
{
    public $enableCsrfValidation = true;
    
    public function actionManager()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }
    
    
    public function actionManagereditsave()
    {
        
        $primaryKey = Yii::$service->customer->getPrimaryKey();
        $_id = Yii::$app->request->post($primaryKey);
        if ($_id) {
            
            $this->customerHasRole($_id);
        } 
        $data = $this->getBlock('manager')->save();
    }
    
    
    /**
     * 当前用户是否有操作权限
     */
    public function customerHasRole($_id){
        $systemConfig = Yii::$service->systemConfig->getByPrimaryKey($_id);
        $type = isset($systemConfig['type']) ? $systemConfig['type'] : '';
        $customer_bdmin_user_id = isset($systemConfig['user_id']) ? $systemConfig['user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$customer_bdmin_user_id || $currentUserId != $customer_bdmin_user_id || $type != 'bdmin') {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to edit this customer'),
            ]);
            exit;
        }
        unset($customer);
    }
    
    
}
