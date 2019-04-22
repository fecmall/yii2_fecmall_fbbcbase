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
class OrdercancelController extends SalesController
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
    
    public function actionManagercancelaccept(){
        $order_ids = Yii::$app->request->post('order_ids');
        $order_id_arr = explode(',', $order_ids);
        foreach ($order_id_arr as $order_id ) {
            
            
            $innerTransaction = Yii::$app->db->beginTransaction();
            try { 
                if (!Yii::$service->order->process->bdminAuditCancelAcceptById($order_id)) {
                    throw new \Exception('audit cancel order accept fail');
                }
                $innerTransaction->commit();
            } catch (\Exception $e) {
                $innerTransaction->rollBack();
                
                echo  json_encode([
                    'statusCode' => '300',
                    'message' => Yii::$service->page->translate->__('audit cancel order accept fail'),
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
    
    public function actionManagercancelrefuse(){
        $order_ids = Yii::$app->request->post('order_ids');
        $order_id_arr = explode(',', $order_ids);
        foreach ($order_id_arr as $order_id ) {
            $innerTransaction = Yii::$app->db->beginTransaction();
            try { 
                if (!Yii::$service->order->process->bdminAuditCancelRefuseById($order_id)) {
                    throw new \Exception('audit cancel order refuse fail');
                }
                $innerTransaction->commit();
            } catch (\Exception $e) {
                $innerTransaction->rollBack();
                
                echo  json_encode([
                    'statusCode' => '300',
                    'message' => Yii::$service->page->translate->__('audit cancel order refuse fail'),
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
    public function orderHasRole($order_id){
        $order = Yii::$service->order->getByPrimaryKey($order_id);
        $order_bdmin_user_id = isset($order['bdmin_user_id']) ? $order['bdmin_user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$order_bdmin_user_id || $currentUserId != $order_bdmin_user_id) {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to edit this order'),
            ]);
            exit;
        }
        unset($order);
    }
    
    
}
