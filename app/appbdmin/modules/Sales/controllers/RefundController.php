<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Sales\controllers;

use fbbcbase\app\appbdmin\modules\Sales\SalesController;
use Yii;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class RefundController extends SalesController
{
    public $enableCsrfValidation = true;
    
    public function actionManager()
    {
        $data = $this->getBlock()->getLastData();
    
        return $this->render($this->action->id, $data);
    }
    
    public function actionManageredit()
    {
        $primaryKey = Yii::$service->refund->getPrimaryKey();
        $refund_id = Yii::$app->request->get($primaryKey);
        $this->orderHasRole($refund_id);
        
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }
    
    public function actionManagereditsave()
    {
          
        $primaryKey = Yii::$service->refund->getPrimaryKey();
        $editFormData = Yii::$app->request->post('editFormData');
        $refund_id = $editFormData['id'];
        $this->orderHasRole($refund_id);
        
        $data = $this->getBlock('manageredit')->save();
    }
    
    public function actionManageraccept(){
        $ids = Yii::$app->request->post('ids');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $refund_id ) {
            $this->orderHasRole($refund_id);
            $innerTransaction = Yii::$app->db->beginTransaction();
            try { 
                if (!Yii::$service->refund->payReturnRefund($refund_id, 'bdmin')) {
                    throw new \Exception('pay refund fail');
                }
                $innerTransaction->commit();
            } catch (\Exception $e) {
                $innerTransaction->rollBack();
                
                echo  json_encode([
                    'statusCode' => '300',
                    'message' => Yii::$service->page->translate->__('pay refund fail'),
                ]);
                exit;
            }
            
        }
        echo  json_encode([
            'statusCode' => '200',
            'message' => Yii::$service->page->translate->__('Save Success'),
        ]);
        exit;
    }
    /**
     * 当前用户是否有操作该产品的权限
     */
    public function orderHasRole($refund_id){
        $refund = Yii::$service->refund->getByPrimaryKey($refund_id, 'bdmin');
        $refund_bdmin_user_id = isset($refund['bdmin_user_id']) ? $refund['bdmin_user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$refund_bdmin_user_id || $currentUserId != $refund_bdmin_user_id) {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to edit this refund'),
            ]);
            exit;
        }
        unset($afterSale);
    }
    
}
