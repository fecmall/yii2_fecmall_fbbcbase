<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appbdmin\modules\Customer\controllers;

use fbbcbase\app\appbdmin\modules\Customer\CustomerController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class AccountController extends CustomerController
{
    public $enableCsrfValidation = true;
    
    public function actionIndex()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }
    
    public function actionManageredit()
    {
        
        $primaryKey = Yii::$service->customer->getPrimaryKey();
        $customer_id = Yii::$app->request->get($primaryKey);
        $this->customerHasRole($customer_id);
        
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }
    
    public function actionManageradd()
    {
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }
    
    public function actionManageraddsave()
    {
        
        $data = $this->getBlock('manageradd')->save();
    }
    
    
    /**
     * 当前用户是否有操作权限
     */
    public function customerHasRole($customer_id){
        $customer = Yii::$service->customer->getByPrimaryKey($customer_id);
        $customer_bdmin_user_id = isset($customer['bdmin_user_id']) ? $customer['bdmin_user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$customer_bdmin_user_id || $currentUserId != $customer_bdmin_user_id) {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to edit this customer'),
            ]);
            exit;
        }
        unset($customer);
    }
    
    public function actionManagereditsave()
    {
        $primaryKey = Yii::$service->customer->getPrimaryKey();
        $editFormData = Yii::$app->request->post('editFormData');
        $customer_id = $editFormData[$primaryKey];
        $this->customerHasRole($customer_id);
        
        $data = $this->getBlock('manageredit')->save();
    }
    
    
    public function actionManagerdelete()
    {
        // 暂时供应商无权操作
        echo json_encode([
            'statusCode' => '300',
            'message' => Yii::$service->page->translate->__('You do not have role to remove this customer') ,
        ]);
        exit;
        
        // 操作权限
        $primaryKey = Yii::$service->customer->getPrimaryKey();
        $customer_id = Yii::$app->request->get($primaryKey);
        $customer_ids = Yii::$app->request->post($primaryKey.'s');
        if (!$customer_id && !$customer_ids) {
            echo json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to remove this customer') ,
            ]);
            exit;
        }else if ($customer_id) {
            // 是否有操作权限
            $this->customerHasRole($customer_id);
        } else if ($customer_ids ) {
            $ids = explode(',', $customer_ids);
            if (is_array($ids) && !empty($ids)) {
                foreach ($ids as $customer_id) {
                    // 是否有操作这个产品的权限
                    $this->customerHasRole($customer_id);
                }
            }
        }
        
        $this->getBlock('manageredit')->delete();
    }
    
}
