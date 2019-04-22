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
class ReturnrefundController extends SalesController
{
    public $enableCsrfValidation = true;
    
    public function actionManager()
    {
        $data = $this->getBlock()->getLastData();
    
        return $this->render($this->action->id, $data);
    }
    
    public function actionManageredit()
    {
        $primaryKey = Yii::$service->order->getPrimaryKey();
        $order_id = Yii::$app->request->get($primaryKey);
        $this->orderHasRole($order_id);
        
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }
    
    public function actionManageraccept(){
        $ids = Yii::$app->request->post('ids');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $as_id ) {
            $this->orderHasRole($as_id);
            $innerTransaction = Yii::$app->db->beginTransaction();
            try { 
                if (!Yii::$service->order->afterSale->bdminReceiveReturnByAsId($as_id)) {
                    throw new \Exception('receive return accept fail');
                }
                $innerTransaction->commit();
            } catch (\Exception $e) {
                $innerTransaction->rollBack();
                
                echo  json_encode([
                    'statusCode' => '300',
                    'message' => Yii::$service->page->translate->__('receive return accept fail'),
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
    public function orderHasRole($as_id){
        $afterSale = Yii::$service->order->afterSale->getByPrimaryKey($as_id);
        $af_bdmin_user_id = isset($afterSale['bdmin_user_id']) ? $afterSale['bdmin_user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$af_bdmin_user_id || $currentUserId != $af_bdmin_user_id) {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to edit this after sale return'),
            ]);
            exit;
        }
        unset($afterSale);
    }
    
}
